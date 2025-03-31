<?php

namespace Danupe\Core\Classes;

class View
{

    public function get(string $pluginOrProject, string $path, $data = [])
    {
        extract($data);
        include danupe()->path()->base() . danupe()->path()->plugin($pluginOrProject) . '/src/views/' . $path . '.php';
    }

    public function render(string $pluginOrProject, string $path, $data = [])
    {
        ob_start();
        include danupe()->path()->base() . danupe()->path()->plugin($pluginOrProject) . '/src/views/' . $path . '.php';
        return ob_get_clean();
    }

}
