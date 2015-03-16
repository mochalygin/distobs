<?php

define('PRIVATE_KEY_BITS', 1024);

$config['db'] = array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../var/db.sqlite3',
    ),
);
