<?php

namespace Danube\Core\Classes;

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

    public static function plugin(string $pluginName)
    {
        foreach (Env::getArray('DANUPE_PLUGINS') as $plugin) {
            $pluginNameFromArray = explode('/', $plugin);
            $pluginNameFromArray = $pluginNameFromArray[count($pluginNameFromArray) - 1] ?? null;
            if ($pluginName === $pluginNameFromArray) {
                return $plugin;
            }
        }
        return null;
    }

    public static function plugins(){
        return Env::getArray('DANUPE_PLUGINS');
    }

    public static function getPluginNameFromPath(string $path)
    {
        $pluginName = explode('/', $path);
        return $pluginName[count($pluginName) - 1] ?? null;
    }
}