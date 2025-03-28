<?php

namespace Danupe\Core\Classes;

class File
{

    public static function exists(string $path): bool
    {
        return file_exists($path);
    }
    public static function getContents(string $path): string
    {
        return file_get_contents($path);
    }
    public static function putContents(string $path, string $contents): void
    {
        file_put_contents($path, $contents);
    }

    public static function get(string $path): array
    {
        return require $path;
    }
}
