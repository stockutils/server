<?php

/** @var Router $router */
use Minute\Model\Permission;
use Minute\Routing\Router;

$router->get('/admin/server', null, 'admin', 'm_configs[type] as configs')
       ->setReadPermission('configs', 'admin')->setDefault('type', 'server');
$router->post('/admin/server', null, 'admin', 'm_configs as configs')
       ->setAllPermissions('configs', 'admin');
