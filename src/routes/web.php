<?php
use Danupe\Core\Classes\Route;
use Danupe\Core\Classes\Request;
use Danupe\Core\Middlewares\CsrfMiddleware;

$router = new Route();
$request = new Request();

$routes = danupe()->route()->getAll();
$systemMiddlewares = danupe()->config()->getAllByKey('middlewares', true);

if (!sizeof($routes)) {
    $router->get('/', function ($request) {
        echo "No routes in database";
        exit;
    });
} else {
    foreach ($routes as $route => $handler) {
        $method = strtolower($handler['method']);  // Hole die Methode (GET, POST, etc.)
        $parsedRoute = preg_replace('/\/\d+/', '/{id}', $route);  // Ersetze IDs durch {id} Platzhalter
        $parsedRoute = preg_replace('/\/\{id\}$/', '[/{id}]', $parsedRoute);  // Spezifische Behandlung für Routen mit {id}

        // Füge die Route hinzu
        $routeInstance = $router->$method($parsedRoute, danupe()->data()->get($handler, 'controller') . ':' . danupe()->data()->get($handler, 'action'));


        // Hole die Middlewares aus der Konfiguration der Route
        $middlewares = danupe()->data()->get($handler, 'middlewares') ?? [];
        // Füge jede Middleware hinzu
        if (!empty($middlewares)) {
            foreach ($middlewares as $middlewareClass) {
                $middlewareClass = danupe()->data()->get($systemMiddlewares, $middlewareClass);
                if ($middlewareClass) {
                    $routeInstance->add($middlewareClass);
                }
            }
        }

        // Füge CSRF Middleware hinzu
        $routeInstance->add(CsrfMiddleware::class);
    }
}

// Dispatch der Anfrage
if (php_sapi_name() !== 'cli') {
    $router->dispatch($request);
}