<?php

$loader = require_once __DIR__.'/../vendor/autoload.php'; 
$loader->add('DistObsNet', __DIR__.'/../src');

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/dev.php';
require __DIR__.'/../src/controllers.php';

$app->run(); 

?>