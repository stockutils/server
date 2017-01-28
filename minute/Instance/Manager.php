<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 11/30/2016
 * Time: 8:29 AM
 */
namespace Minute\Instance {

    use Minute\Aws\Client;
    use Minute\Config\Config;
    use Minute\Error\ServerError;
    use Minute\Log\LoggerEx;

    class Manager {
        const Key          = 'server';
        const port         = 80;
        const minTime      = 15;
        const maxInstances = 10;

        /**
         * @var \Aws\Ec2\Ec2Client
         */
        protected $ec2Client;
        /**
         * @var array
         */
        protected $image;
        /**
         * @var Config
         */
        private $config;
        /**
         * @var LoggerEx
         */
        private $logger;

        /**
         * Manager constructor.
         *
         * @param Config $config
         * @param Client $client
         *
         * @param LoggerEx $logger
         *
         * @throws ServerError
         */
        public function __construct(Config $config, Client $client, LoggerEx $logger) {
            $this->config = $config;
            $this->logger = $logger;

            if ($ec2config = $this->config->get(self::Key . '/config')) {
                $this->ec2Client = $client->getEc2Client($ec2config);

                if (!($this->image = $ec2config['image'])) {
                    throw new ServerError('Please specify image in ' . self::Key . '/config/image');
                }
            } else {
                throw new ServerError('Please specify ec2 credentials in ' . self::Key . '/config');
            }
        }

        public function getInstancesByType($state) {
            $filters[] = ['Name' => 'image-id', 'Values' => [$this->image['ImageId']]];

            if (!empty($state)) {
                $filters[] = ['Name' => 'instance-state-name', 'Values' => [$state]];
            }

            return $this->ec2Client->getIterator('DescribeInstances', ['Filters' => $filters]);
        }

        public function findAvailable($sortBy = null) {
            foreach ($this->getInstancesByType('running') as $reservation) {
                foreach ($reservation['Instances'] as $instance) {
                    if ($status = $this->queryInstance($instance, ['cmd' => 'status'])) {
                        if (@$status['status'] == 'available') {
                            $instances[] = array_merge($instance, $status);
                        }
                    }
                }
            }

            return !empty($instances) ? (!empty($sortBy) ? $this->sortInstances($instances, $sortBy) : $instances) : false;
        }

        public function addInstance($urgent) {
            $count = 0;

            foreach ($this->getInstancesByType('') as $reservation) {
                foreach ($reservation['Instances'] as $instance) {
                    if (preg_match('/pending|running/i', $instance['State']['Name'])) {
                        $count++;
                    }
                }
            }

            if ($count < self::maxInstances) {
                $key = sprintf("%s/lastQueued%s", self::Key, $urgent ? 'Reserved' : 'Spot');

                if ((time() - $this->config->get($key, 0)) > 60 * self::minTime) {
                    if ($newInstance = $urgent ? $this->addReservedInstance() : $this->addSpotInstance()) {
                        $this->config->set($key, time(), true);

                        return $newInstance;
                    }
                }
            } else {
                $this->logger->critical("Already running $count instances and got request for more");
            }

            return false;
        }

        public function queryInstance($instance, $params) {
            try {
                $client = new \GuzzleHttp\Client(['defaults' => ['timeout' => 3, 'connect_timeout' => 5]]);
                $req    = $client->get($this->getInstanceUrl($instance, $params));
                $res    = $req->getBody();

                if ($response = json_decode($res, true)) {
                    return array_merge(['status' => 'error'], $response);
                }
            } catch (\Exception $e) {
                $this->terminate($instance, true);
            }

            return ['status' => 'error'];
        }

        public function terminate($instance, $force = false) {
            if ($launchTime = @$instance['LaunchTime']) {
                if (time() - strtotime($launchTime) < 15 * 60) { //don't terminate instances launched in the last 15 minutes
                    return false;
                } elseif (!$force) {
                    if (((time() - strtotime($launchTime)) % 3600) < (3600 - 10 * 60)) {
                        return false;
                    }
                }
            }

            $terminated = false;//$this->ec2Client->terminateInstances(['InstanceIds' => [$instance['InstanceId']]]);

            return $terminated;
        }

        private function getInstanceUrl($instance, $params) {
            if (!empty($params['cmd'])) {
                $cmd = $params['cmd'];
                unset($params['cmd']);
            }

            return sprintf('http://%s:%d/%s?%s', $instance['PublicIpAddress'], self::port, $cmd ?? '', http_build_query($params));
        }

        private function sortInstances($instances, $sortBy, $reverse = false) {
            usort($instances, function ($a, $b) use ($sortBy) { return ($a[$sortBy] === $b[$sortBy]) ? 0 : ($a[$sortBy] < $b[$sortBy] ? -1 : 1); });

            return $instances[$reverse ? count($instances) - 1 : 0];
        }

        private function addSpotInstance() {
            $params  = ['ProductDescriptions' => [$this->image['OS'] ?? 'Windows'], 'InstanceTypes' => [$this->image['InstanceType']], 'StartTime' => time() - (60 * 60 * 2)];
            $prices  = $this->ec2Client->describeSpotPriceHistory($params);
            $price   = @($prices['SpotPriceHistory'][0]['SpotPrice'] ?: 0.1) + 0.01;
            $request = $this->ec2Client->requestSpotInstances(array_merge(['LaunchSpecification' => $this->image], ['SpotPrice' => "$price", 'ValidUntil' => date('c', (time() + (30 * 60)))]));

            return @$request['SpotInstanceRequests'][0]['SpotInstanceRequestId'];
        }

        private function addReservedInstance() {
            $request = $this->ec2Client->runInstances(array_merge($this->image, ['MinCount' => 1, 'MaxCount' => 1]));

            return @$request['Instances'][0]['InstanceId'];
        }
    }
}