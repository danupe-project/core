<?php

if (!function_exists('dd')) {
    function dd($data)
    {
        dump($data);
        exit;
    }
}

if (!function_exists('config')) {
    $configInstance = null;

    function config($key, $default = '')
    {
        global $configInstance;

        if ($configInstance === null) {
            $configInstance = new \Danupe\Core\Classes\Config();
        }

        return $configInstance->get($key, $default);
    }
}

if (!function_exists('env')) {
    $envInstance = null;

    function env($key, $default = '')
    {
        global $envInstance;

        if ($envInstance === null) {
            $envInstance = new Danupe\Core\Classes\Env();
        }

        return $envInstance::get($key, $default);
    }
}

if (!function_exists('envArray')) {

    function envArray($key, $default = [], $delimiter='')
    {
        global $envInstance;

        if ($envInstance === null) {
            $envInstance = new Danupe\Core\Classes\Env();
        }

        return $envInstance::getArray($key, $default, $delimiter);
    }
}

if (!function_exists('d')) {
    function d($data)
    {
        dump($data);
    }
}

if (!function_exists('dump')) {
    function dump($data)
    {
        $trace = debug_backtrace();
        $caller = $trace[0];
        if ($caller['function'] === 'dd' || $caller['function'] === 'd' || $caller['function'] === 'dump') {
            $caller = $trace[1];
        }

        if (php_sapi_name() == 'cli') {
            var_dump($data);
            echo "Called from: " . $caller['file'] . " on line " . $caller['line'] . "\n\n";
        } else {
            echo "<div  style='background: #222; color:#00cc00; padding:5px;  font-size:12px; font-family:verdana'>DEBUG:";
            echo "<pre style='margin:0; font-weight:bold; font-size:14px; font-family:verdana'>";
            print_r($data);
            echo "</pre>";

            echo "\nStack trace:\n";
            $i = 0;
            foreach ($trace as $step) {
                if ($i < 3) {
                    echo $step['file'] . " (" . $step['line'] . "): " . (isset($step['function']) ? $step['function'] : 'N/A') . "<br />";
                    $i++;
                }
            }
            echo "Called from: " . $caller['file'] . " on line " . $caller['line'] . "<br /><br />";
            echo "</div>";
        }
    }
}
