<?php

namespace Danupe\Core\Classes;

class Danupe
{
    private static $instances = [];

    public function language()
    {
        return $this->getInstance(Language::class);
    }

    public function view()
    {
        return $this->getInstance(View::class);
    }

    public function table()
    {
        return $this->getInstance(Table::class);
    }

    public function input()
    {
        return $this->getInstance(Input::class);
    }

    public function data()
    {
        return $this->getInstance(Data::class);
    }

    public function config()
    {
        return $this->getInstance(Config::class);
    }

    public function env()
    {
        return $this->getInstance(Env::class);
    }

    public function file()
    {
        return $this->getInstance(File::class);
    }

    public function path()
    {
        return $this->getInstance(Path::class);
    }

    public function session()
    {
        return $this->getInstance(Session::class);
    }

    public function helper()
    {
        return $this->getInstance(Helper::class);
    }

    public function cache()
    {
        return $this->getInstance(Cache::class);
    }

    public function route()
    {
        return $this->getInstance(Route::class);
    }

    public function plugin(string $plugin, string $class)
    {
        return $this->module('Danupe\\Plugin\\', $plugin, $class);
    }

    public function project(string $project, string $class)
    {
        return $this->module('Danupe\\Project\\', $project, $class);
    }

    public function module(string $namespace, string $module, string $class)
    {
        $moduleName = str_replace(' ', '', ucwords(str_replace('-', ' ', $module)));
        $className = $namespace . $moduleName . '\\Classes\\' . ucwords($class);

        if (!isset(self::$instances[$module][$className])) {
            self::$instances[$module][$className] = new $className();
        }

        return self::$instances[$module][$className];
    }

    private function getInstance(string $class)
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }
}