<?php

if (!function_exists('dd')) {
    function dd($data)
    {
        dump($data);
        exit;
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
        } else {
            echo "<pre>";
            var_dump($data);
            echo "</pre>";

            echo "\nStack trace:\n";
            $i = 0;
            foreach ($trace as $step) {
                if ($i < 3) {
                    echo $step['file'] . " (" . $step['line'] . "): " . (isset($step['function']) ? $step['function'] : 'N/A') . "\n";
                    $i++;
                }
            }
        }
        echo "Called from: " . $caller['file'] . " on line " . $caller['line'] . "\n\n";

    }
}
