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
            print_r($data);
            #echo "Called from: " . $caller['file'] . " on line " . $caller['line'] . "\n\n";
        } else {
            echo "<div style='z-index:100000; position:relative; background: #222; color: #00dd00; padding:4px; margin:4px;  font-size:13px; font-family:arial'>";
            echo "<pre style='margin:0; font-weight:bold; font-size:14px; font-family:verdana;'>";
            print_r($data);
            echo "</pre>";

            // echo "\nStack trace:\n";
            // $i = 0;
            // foreach ($trace as $step) {
            //     if ($i < 3) {
            //         echo $step['file'] . " (" . $step['line'] . "): " . (isset($step['function']) ? $step['function'] : 'N/A') . "<br />";
            //         $i++;
            //     }
            // }
            echo $caller['file'] . " on line " . $caller['line'];
            echo "</div>";
        }
    }
}

$danupeInstance = null;
function danupe()
{
    global $danupeInstance;

    if ($danupeInstance === null) {
        $danupeInstance = new \Danupe\Core\Classes\Danupe();
    }

    return $danupeInstance;
}
