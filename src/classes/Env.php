<?php

namespace Danupe\Core\Classes;

class Env
{

    public function __construct()
    {
        self::init();
    }

    public static function init(string $path = ''): void
    {
        if ($path === '') {
            $path = danupe()->path()->base() . '/.env';
        }
        if (!file_exists($path)) {
            throw new \RuntimeException("Env file not found: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2) + [null, null];

            if ($key === null || $value === null) {
                continue;
            }

            $key = trim($key);
            $value = trim($value);
            $value = str_replace(['"', "'"], '', $value);

            if (!isset($_ENV[$key]) && !isset($_SERVER[$key])) {
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    public static function getArray(string $key, array $default = [], string $delimiter = ''): array
    {
        if($delimiter === '') {
            $delimiter = ',';
        }

        $array = explode($delimiter, self::get($key, ''));
        foreach ($array as $key => $value) {
            $array[$key] = trim($value);
        }
        if($array === ['']) {
            return $default;
        }
        return $array;
    }

}