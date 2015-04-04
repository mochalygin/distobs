<?php

require __DIR__ . '/../config/config.php';

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

use DistObsNet\Models\NodeManager;
use DistObsNet\Models\SettingsManager;
//use DistObsNet\Models\DataManagerInterface;

//use Silex\Provider\RoutingServiceProvider;
//use Silex\Provider\ValidatorServiceProvider;
//use Silex\Provider\ServiceControllerServiceProvider;
//use Silex\Provider\HttpFragmentServiceProvider;

$app = new Application();

//$app->register(new RoutingServiceProvider());
//$app->register(new ValidatorServiceProvider());
//$app->register(new ServiceControllerServiceProvider());
//$app->register(new HttpFragmentServiceProvider());
$app->register(new DoctrineServiceProvider(), $config['db']);
$app->register(new UrlGeneratorServiceProvider());
$app->register(new TwigServiceProvider());

$app['twig'] = $app->extend('twig', function($twig, $app) {
    // add custom globals, filters, tags, ...
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request_stack']->getMasterRequest()->getBasepath().'/'.$asset;
    }));

    return $twig;
});

$app['keyManager'] = $app->share(function() {
    return new \DistObsNet\Key\KeyManagerDummy();
});

$app['key'] = $app->share(function($app) {
    return new \DistObsNet\Key\KeyDummy($app['keyManager']);
});

$app['publicKey'] = function() {
    return new \DistObsNet\Key\PublicKeyDummy();
};

$app['node'] = $app->share(function($app) {
    return new NodeManager($app);
});

$app['settingsManager'] = $app->share(function($app) {
    return new SettingsManager($app);
});

$app['settings'] = function($app) {
    return $app['settingsManager']->create();
};

$app['publisher'] = null;

return $app;
