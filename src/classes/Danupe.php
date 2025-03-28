<?php

namespace Danupe\Core\Classes;

class Danupe
{

    public function view()
    {
        global $viewInstance;

        if ($viewInstance === null) {
            $viewInstance = new View();
        }
        return $viewInstance;
    }

    public function input()
    {
        global $inputInstance;

        if ($inputInstance === null) {
            $inputInstance = new Input();
        }
        return $inputInstance;
    }

    public function data()
    {
        global $dataInstance;

        if ($dataInstance === null) {
            $dataInstance = new Data();
        }
        return $dataInstance;
    }

    public function config()
    {
        global $configInstance;

        if ($configInstance === null) {
            $configInstance = new Config();
        }
        return $configInstance;
    }

    public function env()
    {
        global $envInstance;

        if ($envInstance === null) {
            $envInstance = new Env();
        }
        return $envInstance;
    }


    public function file()
    {
        global $fileInstance;

        if ($fileInstance === null) {
            $fileInstance = new File();
        }
        return $fileInstance;
    }

    public function path()
    {
        global $pathInstance;

        if ($pathInstance === null) {
            $pathInstance = new Path();
        }
        return $pathInstance;
    }

    public function session()
    {
        global $sessionInstance;

        if ($sessionInstance === null) {
            $sessionInstance = new Session();
        }
        return $sessionInstance;
    }

    public function helper()
    {
        global $helperInstance;

        if ($helperInstance === null) {
            $helperInstance = new Helper();
        }
        return $helperInstance;
    }

}
