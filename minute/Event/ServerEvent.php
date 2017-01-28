<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 12/8/2016
 * Time: 12:17 PM
 */
namespace Minute\Event {

    class ServerEvent extends Event {
        const SERVER_CHECK_PENDING_JOBS = "server.check.pending.jobs";
        /**
         * @var bool
         */
        private $pendingJobs = false;

        /**
         * @return bool
         */
        public function hasPendingJobs(): bool {
            return $this->pendingJobs;
        }

        /**
         * @param bool $pendingJobs
         *
         * @return ServerEvent
         */
        public function setPendingJobs(bool $pendingJobs): ServerEvent {
            $this->pendingJobs = $pendingJobs;

            return $this;
        }
    }
}