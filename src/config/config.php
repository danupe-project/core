<?php

return [
    'prefix' => 'danupe',
    'meta' => [
        'title' => 'Danupe | Great CMS',
        'description' => 'Danupe is a great CMS for developers',
        'keywords' => 'cms, danupe, php, laravel, symfony, wordpress, drupal, joomla',
        'version' => '0.1.0',
    ],
    'danupe' => [
        'clone' => ['Danupe\Core\Classes\ClonePlugin', '', 'Clone a plugin: source, destination, search, replace'], 
    ]
];