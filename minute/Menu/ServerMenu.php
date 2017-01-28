<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/8/2016
 * Time: 7:57 PM
 */
namespace Minute\Menu {

    use App\Model\MAffiliateInfo;
    use Minute\Affiliate\AffiliateInfo;
    use Minute\Cache\QCache;
    use Minute\Config\Config;
    use Minute\Event\ImportEvent;
    use Minute\Session\Session;

    class ServerMenu {
        public function __construct(){
        }

        public function adminLinks(ImportEvent $event) {
            $links = [
                'server' => ['title' => 'Server config', 'icon' => 'fa-cloud', 'priority' => 99, 'parent' => 'expert', 'href' => '/admin/server'],
            ];

            $event->addContent($links);
        }
    }
}