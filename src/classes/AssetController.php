<?php

namespace Danupe\Core\Classes;

class AssetController
{

    public function load($request)
    {

    $config = danupe()->config();
    $allConfig = method_exists($config, 'all') ? $config->all() : [];
    $assetBase = '';
    foreach ($allConfig as $namespace => $conf) {
        if (isset($conf['asset_path'])) {
            $assetBase = $conf['asset_path'];
            break;
        }
    }
        $uri = $request->getUri();
        $assetPath = ltrim(str_replace('/assets/', '', $uri), '/');
        $file = rtrim($assetBase, '/').'/'.$assetPath;

 
        if (!file_exists($file)) {
            http_response_code(404);
            echo "Asset not found";
            exit;
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mimeTypes = [
            'js'   => 'application/javascript',
            'css'  => 'text/css',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2'=> 'font/woff2',
            'ttf'  => 'font/ttf',
            'eot'  => 'application/vnd.ms-fontobject',
            // weitere Typen nach Bedarf
        ];

        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
            if (in_array($ext, ['png','jpg','jpeg','gif','svg','woff','woff2','ttf','eot'])) {
                echo file_get_contents($file);
            } else {
                include $file;
            }
            exit;
        }

        http_response_code(415);
        echo "Unsupported asset type";
        exit;
    }


}
