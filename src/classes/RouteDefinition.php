<?php

namespace Danupe\Core\Classes;

class RouteDefinition
{
    private string $method;
    public string $path;
    private $handler;
    private array $middlewares = [];
    
    // Optionaler Konstruktor, um die Route auch mit einem Standardwert für Middlewares zu versehen
    public function __construct(string $method, string $path, $handler, array $middlewares = [])
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->handler = $handler;
        $this->middlewares = $middlewares;
    }

    /**
     * Überprüft, ob eine Route mit der gegebenen URI und Methode übereinstimmt.
     * 
     * @param string $uri
     * @param string $method
     * @return bool
     */
    public function matches(string $uri, string $method): bool
    {
        // Primitive Matching-Logik, um URI und Methode zu überprüfen
        return $this->method === strtoupper($method) && strtok($uri, '?') === $this->path;
    }

    /**
     * Fügt der Route ein Middleware hinzu.
     * 
     * @param string $middlewareClass
     * @return $this
     */
    public function add(string $middlewareClass): static
    {
        $this->middlewares[] = $middlewareClass;
        return $this;
    }

    /**
     * Gibt den Handler der Route zurück.
     * 
     * @return mixed
     */
    public function getHandler(): mixed
    {
        return $this->handler;
    }

    /**
     * Gibt alle Middlewares der Route zurück.
     * 
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Gibt die HTTP-Methode der Route zurück.
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Gibt den Pfad der Route zurück.
     * 
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Setzt den Handler der Route.
     * 
     * @param mixed $handler
     * @return $this
     */
    public function setHandler(mixed $handler): static
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Setzt die HTTP-Methode der Route.
     * 
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): static
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Setzt den Pfad der Route.
     * 
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): static
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Setzt die Middlewares der Route.
     * 
     * @param array $middlewares
     * @return $this
     */
    public function setMiddlewares(array $middlewares): static
    {
        $this->middlewares = $middlewares;
        return $this;
    }
}
