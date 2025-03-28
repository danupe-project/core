<?php

namespace Danupe\Core\Classes;

class Input
{

    public function get($key = null, $default = null)
    {
        $input = $_REQUEST;

        array_walk_recursive($input, function (&$value) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        });

        return danupe()->data()->get($input, $key, $default);
    }

    public function all()
    {
        $input = $_REQUEST;

        return array_map(function ($value) {
            if (is_array($value)) {
                return array_map('htmlspecialchars', $value);
            }
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }, $input);
    }
}
