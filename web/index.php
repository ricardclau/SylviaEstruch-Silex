<?php

use Silex\Application;

require_once __DIR__ . '/../silex.phar';
require_once __DIR__ . '/../vendor/.composer/autoload.php';
require_once __DIR__ . '/../app/Bootstrap.php';

$app = new Application();

$app['bootstrap'] = $app->share(function($app) {
    return new Bootstrap($app);
});

$app['bootstrap']->run();