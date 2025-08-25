<?php

spl_autoload_register(function ($class) {
    static $namespaces = null;
    static $pathCache = [];

    static $rootDirectory = null;
    if ($rootDirectory === null) {
        $rootDirectory = dirname(__DIR__, 3);
    }

    if ($namespaces === null) {
        $namespaces = [];
        $envFile = $rootDirectory . '/.env';

        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            if (preg_match('/DANUPE_PLUGINS="([^"]+)"/', $envContent, $matches) && !empty($matches[1])) {
                $pluginPaths = explode(',', $matches[1]);
                foreach ($pluginPaths as $pluginPath) {
                    $namespaces[getNamespaceFromPath($pluginPath)] = $pluginPath . "/src/";
                }
            }
        }
    }

    foreach ($namespaces as $namespace => $path) {
        if (strpos($class, $namespace) === 0) {
            $relativeClass = substr($class, strlen($namespace));
            $relativePath  = $path . str_replace('\\', '/', $relativeClass) . '.php';
            $file          = $rootDirectory . '/' . $relativePath;

            if (!isset($pathCache[$file])) {
                $pathCache[$file] = realPathCase($rootDirectory, $relativePath);
            }
            $file = $pathCache[$file];

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

function realPathCase($base, $relativePath)
{
    $parts   = explode('/', trim($relativePath, '/'));
    $current = rtrim($base, '/');

    foreach ($parts as $part) {
        if (!is_dir($current) && !is_file($current)) {
            $current .= '/' . $part;
            continue;
        }

        $found = null;
        foreach (scandir($current) as $item) {
            if (strcasecmp($item, $part) === 0) {
                $found = $item;
                break;
            }
        }
        $current .= '/' . ($found ?? $part);
    }

    return $current;
}
