<?php

namespace Danupe\Core\Classes;

abstract class Controller
{
    protected $request;
    protected $response;
    protected $errors = [];

    public function __construct($request = null, $response = null)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . "/../../views/{$view}.php";

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            $this->addError("View '{$view}' not found.");
        }
    }

    protected function redirect(string $url): void
    {
        danupe()->session()->set('errors', []);
        danupe()->session()->set('success', []);
        header("Location: {$url}");
        exit;
    }

    protected function notFound(): void
    {
        http_response_code(404);
        echo "404 Not Found";
        exit;
    }

    protected function errorResponse(string $message, int $code = 500): void
    {
        http_response_code($code);
        echo $message;
        exit;
    }

    protected function redirectWithErrors(string $url, string|array $errors = []): void
    {
        danupe()->session()->set('success', []);
        danupe()->session()->set('errors', $errors);
        header("Location: {$url}");
        exit;
    }

    protected function redirectWithSuccess(string $url, string|array $messages = []): void
    {
        danupe()->session()->set('errors', []);
        danupe()->session()->set('success', $messages);
        header("Location: {$url}");
        exit;
    }

    protected function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
