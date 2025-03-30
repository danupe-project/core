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
        if($routes[$path]['type']=="javascript"){
            header('Content-Type: application/javascript');
            include danupe()->path()->base().$routeData;
        }
        exit;
    }
}
