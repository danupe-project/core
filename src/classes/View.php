<?php

namespace Danupe\Core\Classes;

class View
{

    public function get(string $pluginOrProject, string $path, $data = [])
    {
        extract($data);
        include danupe()->path()->base() . danupe()->path()->plugin($pluginOrProject) . '/src/views/' . $path . '.php';
    }

}
