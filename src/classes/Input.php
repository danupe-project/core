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

    public function has($key)
    {
        return isset($_REQUEST[$key]);
    }

    public function only(array $keys)
    {
        $input = $this->all();
        return array_intersect_key($input, array_flip($keys));
    }

    public function except(array $keys)
    {
        $input = $this->all();
        return array_diff_key($input, array_flip($keys));
    }
}
