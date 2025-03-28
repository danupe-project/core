<?php
require __DIR__.'/helper.php';
require dirname(__DIR__, 4). '/vendor/autoload.php';

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

use Slim\Factory\AppFactory;
$app = AppFactory::create();

include __DIR__.'/routes/web.php';