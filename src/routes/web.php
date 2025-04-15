<?php

$routes = danupe()->route()->getAll();
$systemMiddlewares = danupe()->config()->getAllByKey('middlewares', true);

if(!sizeof($routes)) {
    $app->get('/', function ($request, $response) {
        echo "no routes in database";
        exit;
    });
}


foreach ($routes as $route => $handler) {
    $method = strtolower($handler['method']);

    $parsedRoute = preg_replace('/\/\d+/', '/{id}', $route);
    $parsedRoute = preg_replace('/\/\{id\}$/', '[/{id}]', $parsedRoute);

    $routeInstance = $app->$method($parsedRoute, danupe()->data()->get($handler,'controller') . ':' . danupe()->data()->get($handler,'action'));
    $middlewares = danupe()->data()->get($handler,'middlewares') ?? [];
    if (!empty($middlewares)) {
        foreach ($middlewares as $middlewareClass) {
            $middlewareClass = danupe()->data()->get($systemMiddlewares, $middlewareClass);
            if ($middlewareClass) {
                $routeInstance->add(new $middlewareClass());
            }
        }
    }

    $routeInstance->add(Danupe\Core\Middlewares\CsrfMiddleware::class);
}
