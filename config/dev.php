<?php

//use Silex\Provider\WebProfilerServiceProvider;

// include the prod configuration
require __DIR__.'/prod.php';

// enable the debug mode
$app['debug'] = true;

ini_set('display_errors', true);

//$app->register(new WebProfilerServiceProvider(), array(
//    'profiler.cache_dir' => __DIR__.'/../var/cache/profiler',
//));
