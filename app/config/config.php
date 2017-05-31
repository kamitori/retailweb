<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

/**
 * Ultility functions & constants
 */
require_once APP_PATH . "/app/ultility.php";

return new \Phalcon\Config(array(
    'database' => array(
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'banhmisub_pos',
            'password'    => '@AnvyTeam5',
            'dbname'      => 'banhmisub_pos',
            'charset'     => 'utf8',
    ),
    'databases' => array(
        'retailweb' => array(
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => 'retailweb',
            'charset'     => 'utf8',
        ),
        'banhmisub' => array(
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'banhmisub_pos',
            'password'    => '@AnvyTeam5',
            'dbname'      => 'banhmisub_pos',
            'charset'     => 'utf8',
        ),
        'newpos' => array(
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'bmspos_pos',
            'password'    => '123456',
            'dbname'      => 'bmspos_pos',
            'charset'     => 'utf8',
        ),
        'vimpact_demo' => array(
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'vimpact_demopos',
            'password'    => '@AnvyTeam5',
            'dbname'      => 'vimpact_demopos',
            'charset'     => 'utf8',
        )
    ),
    'application' => array(
        'appDir'         => APP_PATH . '/app/',
        'controllersDir' => APP_PATH . '/app/controllers/frontend',
        'adminControllersDir' => APP_PATH . '/app/controllers/admin',
        'posControllersDir'   => APP_PATH . '/app/controllers/pos',
        'poscashControllersDir'   => APP_PATH . '/app/controllers/poscash',
        'kioskControllersDir'   => APP_PATH . '/app/controllers/kiosk',
        'servicesControllersDir' => APP_PATH . '/app/controllers/services',
        'modelsDir'      => APP_PATH . '/app/models/',
        'migrationsDir'  => APP_PATH . '/app/migrations/',
        'viewsDir'       => APP_PATH . '/app/views/',
        'pluginDir'      => APP_PATH . '/app/plugin/',
        'libraryDir'     => APP_PATH . '/app/library/',
        'cacheDir'       => APP_PATH . '/app/cache/',
        'baseUri'        => URL,
        'cryptSalt'      => 'a1y2v@n%',
        'viewCacheTime'  => 86400
    ),
));
