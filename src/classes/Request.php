<?php

namespace Danupe\Core\Classes;

class Request
{
    public string $method;
    public string $uri;
    public array $query;
    public array $body;
    public array $server;
    public array $files;
    public array $cookies;
    public array $headers;

    // Add a property to avoid dynamic property creation
    private array $customData = [];

    // Add a property to store the CSRF token
    private ?string $_csrf_token = null;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $this->query = $_GET;
        $this->body = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        if (function_exists('getallheaders')) {
            $this->headers = getallheaders();
        } else {
            $this->headers = [];
        }

        // Initialize the CSRF token
        $this->_csrf_token = $_SESSION['_csrf_token'] ?? null;
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function has(string $key): bool
    {
        return isset($this->query[$key]) || isset($this->body[$key]);
    }

    public function fullUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . ($_SERVER['REQUEST_URI'] ?? '/');
    }


    public function getMethod(): string
    {
        return $this->method;
    }    public function getUri(): string
    {
        return $this->uri;
    }

    protected function getUriFromServer(): string
    {
        $uri = $this->serverParams['REQUEST_URI'] ?? '';
        // Entfernt Query-Parameter
        $uri = preg_replace('/\?.*/', '', $uri);
        return $uri;
    }

    /**
     * Holt die HTTP-Methode aus $_SERVER
     */
    protected function getMethodFromServer(): string
    {
        return strtoupper($this->serverParams['REQUEST_METHOD'] ?? 'GET');
    }

     // Session Handling
     public function getSession(): array
     {
         if (session_status() === PHP_SESSION_NONE) {
             session_start();
         }
 
         return $_SESSION;
     }
 
     // Getter für den Body des Requests
     public function getParsedBody(): array
     {
         return $_POST; // Beispiel für den Zugriff auf den Body
     }
 
 
     // Setter für benutzerdefinierte Request-Daten
     public function set(string $key, $value): void
     {
         $this->$key = $value;
     }

    // Getter for session data with a default value
    public function getSessionData(string $key, $default = null)
    {
        return $this->getSession()[$key] ?? $default;
    }

    // Setter for session data
    public function setSessionData(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
}

