<?php

namespace Danupe\Core\Classes;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AssetController
{

    public function load(Request $request, Response $response, array $arg)
    {

        $routes = danupe()->route()->getAll();
        $path = $request->getUri()->getPath();
        $routeData = $routes[$path]['path'];
        if($routes[$path]['type']=="javascript" || $routes[$path]['type']=="js"){
            header('Content-Type: application/javascript');
            include danupe()->path()->base().$routeData;
        }
        if($routes[$path]['type']=="css"){
            header('Content-Type: text/css');
            include danupe()->path()->base().$routeData;
        }
        if($routes[$path]['type']=="font"){
            header('Content-Type: font/woff2');
            include danupe()->path()->base().$routeData;
        }
        if($routes[$path]['type']=="png"){
            header('Content-Type: image/png');
            echo file_get_contents(danupe()->path()->base().$routeData);
        }
        exit;
    }
}
