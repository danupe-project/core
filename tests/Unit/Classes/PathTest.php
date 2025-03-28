<?php

namespace Danupe\Core\Test\Classes\PathTest;

use Danupe\Core\Classes\Path;
use Danupe\Core\TestCase;
class PathTest extends TestCase
{
    public function testBase()
    {
        $base = danupe()->path()->base();
        //todo: find better common solution
        $this->assertEquals(dirname(__DIR__, 6), $base);
    }

    public function testPlugin()
    {
        $package = danupe()->path()->plugin('core');
        $this->assertEquals('/development/danupe/core', $package);
    }

    public function testPlugins()
    {
        $plugins = danupe()->path()->plugins();
        $this->assertIsArray($plugins);
        $this->assertContains('/development/danupe/core', $plugins);
    }

    public function testGetPluginNameFromPath()
    {
        $pluginName = danupe()->path()->getPluginNameFromPath('/development/danupe/core');
        $this->assertEquals('core', $pluginName);
    }
}
