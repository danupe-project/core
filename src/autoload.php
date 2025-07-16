<?php

spl_autoload_register(function ($class) {

    $rootDirectory = dirname(__DIR__, 3);

    $envFile = $rootDirectory . '/.env';
    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        preg_match('/DANUPE_PLUGINS="([^"]+)"/', $envContent, $matches);

        $namespaces=[];
        if (!empty($matches[1])) {
            $pluginPaths = explode(',', $matches[1]);
            foreach ($pluginPaths as $pluginPath) {
                $namespaces[getNamespaceFromPath($pluginPath)] = $pluginPath."/src/";
            }
        }
    }

    foreach ($namespaces as $namespace => $path) {
        if (strpos($class, $namespace) === 0) {
            $relativeClass = substr($class, strlen($namespace));
            $file = $rootDirectory . '/' . $path . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }

});