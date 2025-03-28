<?php

use Danupe\Core\Classes\Config;

$config = new Config();
$routes = $config->getRoutes();

$systemMiddlewares = $config->getAllByKey('middlewares', true);

foreach ($routes as $routeGroup) {
    foreach ($routeGroup as $method => $routes) {
        foreach ($routes as $route => $handler) {
            $routeInstance = $app->$method($route, danupe()->data()->get($handler, 'controller') . ':' . danupe()->data()->get($handler, 'action'));
            $middlewares = danupe()->data()->get($handler, 'middlewares', []);
            if (!empty($middlewares)) {
                foreach ($middlewares as $middlewareClass) {

                    $middlewareClass = danupe()->data()->get($systemMiddlewares, $middlewareClass);
                    if($middlewareClass){
                        $routeInstance->add(new $middlewareClass());
                    }

                    $routeInstance->add(Danupe\Core\Middlewares\CsrfMiddleware::class);

                }
            }
        }
    }
}
