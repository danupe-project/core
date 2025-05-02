<?php

namespace Danupe\Core\Classes;

class Route
{
    public function getAll()
    {
        return danupe()->config()->getAllByKey('routes', 1);
    }

    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, callable|array|string $handler): RouteDefinition {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array|string $handler): RouteDefinition {
        return $this->addRoute('POST', $path, $handler);
    }

    public function addRoute(string $method, string $path, callable|array|string $handler): RouteDefinition {
        $routeDef = new RouteDefinition($method, $path, $handler);
        $this->routes[] = $routeDef;
        return $routeDef;
    }

    public function dispatch(\Danupe\Core\Classes\Request $request): void
    {
        $uri = rtrim(parse_url($request->getUri(), PHP_URL_PATH), '/') ?: '/';
        $method = strtolower($request->getMethod());

        foreach ($this->routes as $definition) {
            if (strtolower($definition->getMethod()) === $method) {
                $uri = strtok($uri, '?');
                // Ensure the pattern is properly escaped and valid
                // Zuerst optionale Platzhalter wie [/{id}] behandeln
                $pattern = preg_replace_callback('#\[([^\[\]]+)\]#', function ($m) {
                    return '(?:' . preg_replace('#\{([^/]+)\}#', '(?<$1>[^/]+)', $m[1]) . ')?';
                }, $definition->getPath());
                // Dann Pflicht-Platzhalter {id} ersetzen
                $pattern = preg_replace('#\{([^/]+)\}#', '(?<$1>[^/]+)', $pattern);
                $pattern = "#^" . $pattern . "$#";

                // Validate the generated pattern
                if (@preg_match($pattern, '') === false) {
                    throw new \RuntimeException("Invalid regular expression: $pattern");
                }

                if (preg_match($pattern, $uri, $matches)) {
                    // Extrahiere nur benannte Parameter
                    $params = [];
                    foreach ($matches as $key => $value) {
                        if (!is_int($key)) {
                            $params[] = $value;
                        }
                    }

                    $handler = $definition->getHandler();
                    $middlewares = $definition->getMiddlewares();

                    // Baue die Middleware-Chain
                    $middlewareChain = array_reverse($middlewares);
                    $next = function($request) use ($handler, $params) {
                        if (is_string($handler) && str_contains($handler, ':')) {
                            [$class, $method] = explode(':', $handler);
                            (new $class())->$method($request, ...$params);
                        } elseif (is_callable($handler)) {
                            call_user_func($handler, $request, ...$params);
                        }
                    };
                    foreach ($middlewareChain as $middlewareClass) {
                        $next = function($request) use ($middlewareClass, $next) {
                            $middleware = new $middlewareClass();
                            if (method_exists($middleware, 'handle')) {
                                return $middleware->handle($request, $next);
                            } elseif (is_callable($middleware)) {
                                return $middleware($request, $next);
                            }
                        };
                    }
                    // Starte die Chain
                    $next($request);
                    return;
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function callHandler(callable|array|string $handler, array $request): void {
        if (is_callable($handler)) {
            call_user_func($handler, $request);
        } elseif (is_string($handler) && str_contains($handler, ':')) {
            [$class, $method] = explode(':', $handler);
            (new $class())->$method($request);
        }
    }
}
