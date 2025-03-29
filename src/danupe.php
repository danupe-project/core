<?php
require dirname(__DIR__, 4). '/vendor/autoload.php';
session_start();
require __DIR__.'/helper.php';
danupe()->session()->setCsrf();

use Slim\Factory\AppFactory;
$app = AppFactory::create();

include __DIR__.'/routes/web.php';