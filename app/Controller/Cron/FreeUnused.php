<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 12/8/2016
 * Time: 12:15 PM
 */
namespace App\Controller\Cron {

    use Minute\Event\Dispatcher;
    use Minute\Event\ServerEvent;
    use Minute\Instance\Manager;

    class FreeUnused {
        /**
         * @var Manager
         */
        private $manager;
        /**
         * @var Dispatcher
         */
        private $dispatcher;

        /**
         * FreeUnused constructor.
         *
         * @param Manager $manager
         * @param Dispatcher $dispatcher
         */
        public function __construct(Manager $manager, Dispatcher $dispatcher) {
            $this->manager    = $manager;
            $this->dispatcher = $dispatcher;
        }

        public function freeUnusedInstances() {
            $event = new ServerEvent();
            $this->dispatcher->fire(ServerEvent::SERVER_CHECK_PENDING_JOBS, $event);

            if (!$event->hasPendingJobs()) {
                $available = $this->manager->findAvailable();

                printf("Found %d running instances\n", count($available));

                if (!empty($available)) {
                    for ($i = count($available) - 1; $i >= 0; $i--) {
                        $instance = $available[$i];

                        if ($instance['status'] == 'idle') {
                            printf("Terminating: %s..", trim($instance['PublicIpAddress']));

                            if ($this->manager->terminate($instance, $i > 0)) {
                                print "done!\n";
                            } else {
                                print "failed (probably has time left)\n";
                            }
                        }
                    }
                }
            }
        }
    }
}