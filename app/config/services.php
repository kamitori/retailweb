<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;


use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

$di->set('config', $config);
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

/**
 * Router
 */
$di->set('router', function () use ($config) {
    return require_once $config->application->appDir.'routes.php';
}, true);

/**
 * Setting up the view component
 */
$di->setShared('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));
            $Compiler = $volt->getCompiler();
            $Compiler->addFunction('rand_key','rand_key');
            $Compiler->addFunction('dinhdangtien','display_format_currency');
            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
});

// Set the views cache service
$di->set('viewCache', function () use ($config) {

    // Cache data for one day by default
    $frontCache = new \Phalcon\Cache\Frontend\Output(
        array(
            'lifetime' => $config->application->viewCacheTime
        )
    );

    // Memcached connection settings
    $cache = new \Phalcon\Cache\Backend\Redis(
        $frontCache,
        array(
            'host'  => '127.0.0.1',
            'port'  => 6379,
            'persistent' => false,
        )
    );

    return $cache;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter($config->databases->{DB}->toArray());
});

/**
 * MongoClient service
 */
$di->set('mongo', function () use ($config) {
    return (new MongoClient('127.0.0.1:27017?connectTimeoutMS=300000'))->selectDb(JT_DB);
    // return (new MongoClient('mongodb://sadmin:2016Anvy!@localhost:27017?connectTimeoutMS=300000'))->selectDb(JT_DB);
},true);


// $di->set('mongo', function () {
//     $mongo = new MongoClient("mongodb:///tmp/mongodb-27017.sock,localhost:27017");
//     return $mongo->selectDB(JT_DB);
// }, true);


$di->set('collectionManager', function () use ($config) {
    return new Phalcon\Mvc\Collection\Manager();
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Crypt service
 */
$di->set('crypt', function () use ($config) {
    $crypt = new \Phalcon\Crypt();
    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new RW\Auth\Auth;
});

/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('RW\Controllers');
    return $dispatcher;
});
