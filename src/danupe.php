<?php
require dirname(__DIR__, 4). '/vendor/autoload.php';
require __DIR__.'/helper.php';

use Danube\Core\Classes\Env;
Env::init();


use Slim\Factory\AppFactory;
$app = AppFactory::create();
