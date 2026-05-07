<?php

namespace Danupe\Core\Classes;

class AssetController
{

    public function load($request)
    {
        $uri = rtrim($request->getUri(), '/') ?: '/';
        $allConfig = danupe()->config()->all();
        
        $pathByUri = null;
        $assetBase = '';
        $relativePath = '';


        foreach ($allConfig as $namespace => $conf) {
            $routes = $conf['routes'] ?? [];
            foreach ($routes as $routeKey => $routeConfig) {

                if ($routeKey === $uri) {
                    $pathByUri = $routeConfig;
                    $assetBase = $conf['asset_path'] ?? '';
                    break 2;
                }
                

                if (str_ends_with($routeKey, '*')) {
                    $base = rtrim(substr($routeKey, 0, -1), '/');
                    if ($uri === $base || str_starts_with($uri, $base . '/')) {
                        $pathByUri = $routeConfig;
                        $assetBase = $conf['asset_path'] ?? '';
                        $relativePath = ltrim(substr($uri, strlen($base)), '/');
                        

                        if (str_contains($relativePath, '..')) {
                            http_response_code(403);
                            echo "Access denied";
                            exit;
                        }
                        break 2;
                    }
                }
            }
        }

        if (!$pathByUri) {
            http_response_code(404);
            echo "Asset configuration not found";
            exit;
        }

        $allowedRoles = $pathByUri['roles'] ?? null;
        $sessionUser = danupe()->session()->get('user');
        $currentRole = $sessionUser['role'] ?? 'guest';

        if (!danupe()->plugin('user', 'role')->isAllowed($currentRole, $allowedRoles)) {
            http_response_code(403);
            echo "403 Forbidden: Sie haben keine Berechtigung für dieses Asset.";
            exit;
        }

        if ($relativePath !== '' && $assetBase !== '') {
            $file = rtrim($assetBase, '/') . '/' . $relativePath;
        } else {
            $file = $_SERVER['DOCUMENT_ROOT'] . ($pathByUri['path'] ?? '');
        }

        if (!$file || !file_exists($file)) {
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
