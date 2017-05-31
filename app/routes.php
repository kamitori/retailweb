<?php
$router = new Phalcon\Mvc\Router(false);

$router->removeExtraSlashes(true);

/*
 ********************
 *   - Frontend -
 ********************
 */

foreach(['','/pos'] as $mod) {    
    $nsp = 'RW\Controllers';
    if($mod == '/pos')
    {
        $nsp = 'RW\Controllers\Pos';
    }
    $router->add($mod.'/{categoryName:[a-zA-Z0-9\_\-]+}/{productName:[a-zA-Z0-9\_\-]+}.html', array(
        'namespace'     => $nsp,
        'controller'    => 'Categories',
        'action'        => 'product',
    ));

    $router->add($mod.'/{categoryName:[a-zA-Z0-9\_\-]+}', array(
        'namespace'     => $nsp,
        'controller'    => 'Categories',
        'action'        => 'index',
    ));

    $router->add($mod.'/{categoryName:[a-zA-Z0-9\_\-]+}/search', array(
        'namespace'     => $nsp,
        'controller'    => 'Categories',
        'action'        => 'index',
    ));

    $router->add($mod.'/pages/{pageName:[a-zA-Z0-9\_\-]+}', array(
        'namespace'     => $nsp,
        'controller'    => 'Pages',
        'action'        => 'index',
    ));
    $router->add($mod.'/drink-station', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'drink'
    ));
    $router->add($mod.'/bms-station', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'bms'
    ));
    //kitchen & kitchen-station mean both drink & bms
    $router->add($mod.'/kitchen', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'kitchen'
    ));
    $router->add($mod.'/kitchen-station', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'kitchen'
    ));
    $router->add($mod.'/manager', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'manager'
    ));
    
    $router->add($mod.'/cart', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'cart'
    ));
    $router->add($mod.'/cartview/{id}', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'cartview'
    ));

    $router->add($mod.'/view-all-carts', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'viewcarts'
    ));

    $router->add($mod.'/screencarts/update_url', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 'updateUrlScreen'
    ));

    $router->add($mod.'/online-station', array(
        'namespace'     => $nsp,
        'controller'    => 'Orders',
        'action'        => 'onlineOrder'
    ));
    $router->add($mod.'/online', array(
        'namespace'     => $nsp,
        'controller'    => 'Orders',
        'action'        => 'onlineOrder'
    ));

    $router->add($mod.'/stations/:action', array(
        'namespace'     => $nsp,
        'controller'    => 'Stations',
        'action'        => 1
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });
    $router->add($mod.'/carts', array(
        'namespace'     => $nsp,
        'controller'    => 'Carts',
        'action'        => 'index'
    ));
    $router->add($mod.'/carts/:action', array(
        'namespace'     => $nsp,
        'controller'    => 'Carts',
        'action'        => 1
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

    $router->add($mod.'/carts/:action/:params', array(
        'namespace'     => $nsp,
        'controller'    => 'Carts',
        'action'        => 1,
        'params'        => 2
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

    $router->add($mod.'/print-order/:params', array(
        'namespace'     => $nsp,
        'controller'    => 'Carts',
        'action'        => 'print',
        'params'        => 1
    ));

    $router->add($mod.'/payment-calculation', array(
        'namespace'     => $nsp,
        'controller'    => 'Carts',
        'action'        => 'paymentCalculation'
    ));

    $router->add($mod.'/detailpro/:action/:params', array(
        'namespace'     => $nsp,
        'controller'    => 'Products',
        'action'        => 1,
        'params'        => 2
    ));
    $router->add($mod.'/favorites', array(
        'namespace'     => $nsp,
        'controller'    => 'Favorites',
        'action'        => 'index'
    ));
    $router->add($mod.'/login', array(
        'namespace'     => $nsp,
        'controller'    => 'Users',
        'action'        => 'login'
    ));
    $router->add($mod.'/favorites/remove', array(
        'namespace'     => $nsp,
        'controller'    => 'Favorites',
        'action'        => 'remove'
    ));
    $router->add($mod.'/orders', array(
        'namespace'     => $nsp,
        'controller'    => 'Orders',
        'action'        => 'index'
    ));
    $router->add($mod.'/lastorders', array(
        'namespace'     => $nsp,
        'controller'    => 'Orders',
        'action'        => 'lastOrders'
    ));
    $router->add($mod.'/lastorders/reorder', array(
        'namespace'     => $nsp,
        'controller'    => 'Orders',
        'action'        => 'reOrders'
    ));
    $router->add($mod.'/orders/:action', array(
        'namespace'     => $nsp,
        'controller'    => 'Orders',
        'action'        => 1
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

    $router->add($mod.'/user/:action', array(
        'namespace'     => $nsp,
        'controller'    => 'Users',
        'action'        => 1
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

    $router->add($mod.'/products/:action/:params', array(
        'namespace'     => $nsp,
        'controller'    => 'Products',
        'action'        => 1,
        'params'        => 2
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

    $router->add($mod.'/staffs/:action/:params', array(
        'namespace'     => $nsp,
        'controller'    => 'Staffs',
        'action'        => 1,
        'params'        => 2
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

    $router->add($mod.'/index/:action', array(
        'namespace'     => $nsp,
        'controller'    => 'Index',
        'action'        => 1
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

    $router->add($mod.'/service/:action', array(
        'namespace'     => $nsp,
        'controller'    => 'Service',
        'action'        => 1
    ))->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });    
}

// $router->add('/', array(
//     'namespace'     => 'RW\Controllers',
//     'controller'    => 'Index',
//     'action'        => 'index',
// ));

$router->add('/testing', array(
    'namespace'     => 'RW\Controllers',
    'controller'    => 'Index',
    'action'        => 'testing',
));

$router->add('/pos', array(
    'namespace'     => 'RW\Controllers\Pos',
    'controller'    => 'Index',
    'action'        => 'index',
));


/*
 ********************
 * - End Frontend -
 ********************
 */

foreach(['Admin','Poscash','Kiosk','Services'] as $module) {
    $uri = strtolower($module);

    $router->add('/'. $uri, array(
        'namespace'     => 'RW\Controllers\\'.$module,
        'controller'    => 'Index',
        'action'        => 'index',
    ));

    $router->add('/'. $uri .'/:controller', array(
        'namespace'     => 'RW\Controllers\\'. $module,
        'controller'    => 1
    ))->convert('controller', function ($controller) {
        return \Phalcon\Text::camelize($controller);
    });

    $router->add('/'. $uri .'/:controller/:action', array(
        'namespace'     => 'RW\Controllers\\'. $module,
        'controller'    => 1,
        'action'        => 2
    ))->convert('controller', function ($controller) {
        return \Phalcon\Text::camelize($controller);
    })->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

    $router->add('/'. $uri .'/:controller/:action/:params', array(
        'namespace'     => 'RW\Controllers\\'. $module,
        'controller'    => 1,
        'action'        => 2,
        'paramsList'        => 3
    ))->convert('controller', function ($controller) {
        return \Phalcon\Text::camelize($controller);
    })->convert('action', function ($action) {
        return lcfirst(\Phalcon\Text::camelize($action));
    });

}

return $router;