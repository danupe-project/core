<?php

return [
    'testkey' => 'testvalue2',
    'more' => [
        'testkey' => 'testvalue2',
    ],
    'routes' => [
        'guest' => [
            'GET' => [
                '/test2' => [
                    'controller' => 'Danupe\Plugin\User\Controllers\HomeController',
                    'action' => 'test2',
                ],
            ],
        ],
    ],
];