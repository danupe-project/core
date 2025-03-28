<?php

namespace Danupe\Core\Classes;

class Session
{
    private array|null $data = [];
    private array $errors = [];

    public function __construct()
    {
        $this->data = &$_SESSION;
    }

    public function start()
    {
        session_start();
    }

    public function setCsrf()
    {
        $this->set('csrf_token', bin2hex(random_bytes(32)));
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function remove(string $key): void
    {
        unset($this->data[$key]);
    }

    public function addError(string $message): void
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

    public function clearErrors(): void
    {
        $this->errors = [];
    }

    public function destroy(): void
    {
        session_destroy();
        $this->data = [];
    }

    public function old(string $key, $default = null)
    {
        $value = danupe()->data()->get(danupe()->session()->get('old'), $key, $default);
        return $value;
    }
}
