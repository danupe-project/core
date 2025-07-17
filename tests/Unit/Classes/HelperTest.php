<?php

use Danupe\Core\Classes\Path;
use Danupe\Core\TestCase;
use Danupe\Core\Classes\Config;
use Danupe\Core\Classes\Helper;

class HelperTest extends TestCase
{

    public function testGetNamespaceFromPath()
    {
        $this->assertEquals(
            'Danupe\Plugin\Text',
            Helper::getNamespaceFromPath('/var/www/html/danupe/plugin-text/src/')
        );

        $this->assertEquals(
            'Test\Project\Homepage',
            Helper::getNamespaceFromPath('/var/www/html/test/project-homepage/src/')
        );
        
    }
}
