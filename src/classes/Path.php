<?php

namespace Danupe\Core\Classes;

class Path
{

    public static function findProjectRoot($startDir = __DIR__)
    {
        while ($startDir !== '/' && !file_exists($startDir . '/.env')) {
            $startDir = dirname($startDir);
        }

        return $startDir !== '/' ? $startDir : null;
    }

    public static function base()
    {
        return self::findProjectRoot();
    }

    public static function plugin(string $pluginName, $fullPath = false)
    {
        foreach (danupe()->env()->getArray('DANUPE_PLUGINS') as $plugin) {
            $pluginNameFromArray = explode('/', $plugin);
            $pluginNameFromArray = $pluginNameFromArray[count($pluginNameFromArray) - 1] ?? null;
            if ($pluginName === $pluginNameFromArray) {
                if($fullPath) {
                    return self::findProjectRoot() . $plugin;
                }
                return $plugin;
            }
        }
        return null;
    }

    public static function plugins(){
        return danupe()->env()->getArray('DANUPE_PLUGINS');
    }

    public static function getPluginNameFromPath(string $path)
    {
        $pluginName = explode('/', $path);
        return $pluginName[count($pluginName) - 1] ?? null;
    }

    public static function getAllFiles(string $path)
    {
        $files = glob($path . '/*');
        $files = array_filter($files, fn($file) => is_file($file));
        return $files;
    }
}