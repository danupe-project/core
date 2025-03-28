<?php

return [
    'testkey' => 'testvalue',
    'more' => [
        'testkey' => 'testvalue',
    ],
    'routes' => [
        'guest' => [
            'GET' => [
                '/test1' => [
                    'controller' => 'Danupe\Plugin\User\Controllers\HomeController',
                    'action' => 'test1',
                ],
            ],
            'POST' => [
                '/testpost1' => [
                    'controller' => 'Danupe\Plugin\User\Controllers\HomeController',
                    'action' => 'testpost1',
                ],
            ],
        ],
    ],
];