<?php

/** @var Binding $binding */
use App\Controller\Swf2Vid\StockVideoPage;
use Minute\Event\AdminEvent;
use Minute\Event\Binding;
use Minute\Menu\ServerMenu;

$binding->addMultiple([
    //debug
    ['event' => AdminEvent::IMPORT_ADMIN_MENU_LINKS, 'handler' => [ServerMenu::class, 'adminLinks']],

    //static event listeners go here
    ['event' => 'IMPORT_PROJECT_INFO', 'handler' => [StockVideoPage::class, 'getProjectInfo'], 'priority' => 0]
]);