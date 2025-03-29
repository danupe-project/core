<?php

$routes = danupe()->route()->getAll();
$systemMiddlewares = danupe()->config()->getAllByKey('middlewares', true);

foreach ($routes as $route => $handler) {
    $method = strtolower($handler['method']);
    $routeInstance = $app->$method($route, $handler['controller'] . ':' . $handler['action']);

    $middlewares = $handler['middlewares'] ?? [];
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
