<?php

namespace Danupe\Core\Classes;

class Config
{
    protected array $config = [];

    public function __construct(string|array $path = "")
    {
        $this->loadConfigs($path);
    }

    private function loadConfigs(string|array $path): void
    {
        $allPlugins = danupe()->path()->plugins();
        if ($path !== '') {
            $allPlugins = is_array($path) ? $path : [$path];
        }

        foreach ($allPlugins as $path) {
            $configDirectory = danupe()->path()->base() . $path . '/src/config';
            if (is_dir($configDirectory)) {
                $configFiles = glob($configDirectory . '/*.php');
                foreach ($configFiles as $file) {
                    if (file_exists($file)) {
                        $pluginName = danupe()->path()->getPluginNameFromPath($path);
                        $this->mergeConfig($file, $pluginName);
                    }
                }
            }
        }
    }

    private function mergeConfig(string $file, string $namespace)
    {
        if (file_exists($file)) {
            $configArray = require $file;
            if (is_array($configArray)) {
                $this->config[$namespace] = array_merge_recursive(
                    $this->config[$namespace] ?? [],
                    $configArray
                );
            }
        }
    }

    public function get(string $key, $default = '')
    {
        $keys = explode('.', $key);
        $config = $this->config;

        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                return $default;
            }
            $config = $config[$key];
        }

        return $config;
    }

    public function all()
    {
        return $this->config;
    }

    public function set(string $key, $value)
    {
        $this->config[$key] = $value;
    }

    public function getAllByKey(string $key = 'routes', $asOneDimensionalArray = false)
    {

        $configData = [];
        foreach ($this->config as $configKey => $config) {
            if (danupe()->data()->get($config, $key)) {
                if(!$asOneDimensionalArray) {
                    $configData[str_replace('plugin-','',$configKey)] = danupe()->data()->get($config, $key);
                }else{
                    $configData[] = danupe()->data()->get($config, $key);
                }
            }
        }

        if ($asOneDimensionalArray) {
            $configData = array_merge(...$configData);
        }

        return $configData;
    }
}
