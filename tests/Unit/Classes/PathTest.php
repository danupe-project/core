<?php

declare(strict_types=1);

namespace Danupe\Core\Tests\Unit\Classes;

use Danupe\Core\Classes\Path;
use Danupe\Core\TestCase;

class PathTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /** @covers Path::base @covers Path::findProjectRoot */
    public function testBaseReturnsCorrectProjectRoot(): void
    {
        $base = danupe()->path()->base();
        
        $this->assertIsString($base);
        $this->assertFileExists($base . '/.env');
        $this->assertEquals('/app/www', $base);
    }

    /** @covers Path::plugin */
    public function testPluginReturnsCorrectPluginPath(): void
    {
        $packagePath = danupe()->path()->plugin('core');
        
        $this->assertIsString($packagePath);
        $this->assertStringContainsString('core', $packagePath);
    }

    /** @covers Path::plugin */
    public function testPluginReturnsNullForNonExistentPlugin(): void
    {
        $packagePath = danupe()->path()->plugin('non-existent-plugin');
        
        $this->assertNull($packagePath);
    }

    /** @covers Path::plugins */
    public function testPluginsReturnsArrayOfPluginPaths(): void
    {
        $plugins = danupe()->path()->plugins();
        
        $this->assertIsArray($plugins);
        $this->assertNotEmpty($plugins);
        
        $coreFound = false;
        foreach ($plugins as $plugin) {
            if (str_contains($plugin, 'core')) {
                $coreFound = true;
                break;
            }
        }
        $this->assertTrue($coreFound, 'Core plugin should be found in plugins array');
    }

    /** @covers Path::getPluginNameFromPath */
    public function testGetPluginNameFromPathReturnsCorrectName(): void
    {
        $pluginName = danupe()->path()->getPluginNameFromPath('/development/danupe/core');
        
        $this->assertEquals('core', $pluginName);
        $this->assertIsString($pluginName);
    }

    /** @covers Path::getPluginNameFromPath */
    public function testGetPluginNameFromComplexPath(): void
    {
        $pluginName = danupe()->path()->getPluginNameFromPath('/var/www/html/plugins/user-management');
        
        $this->assertEquals('user-management', $pluginName);
    }

    /** @covers Path::getPluginNameFromPath */
    public function testGetPluginNameFromPathWithTrailingSlash(): void
    {
        $pluginName = danupe()->path()->getPluginNameFromPath('/development/danupe/plugin-database');
        
        $this->assertEquals('plugin-database', $pluginName);
    }

    /** @covers Path::getPluginNameFromPath */
    public function testGetPluginNameFromEmptyPathReturnsEmptyString(): void
    {
        $pluginName = danupe()->path()->getPluginNameFromPath('');
        
        $this->assertEquals('', $pluginName);
    }

    /** @covers Path::findProjectRoot */
    public function testFindProjectRootFromCurrentDirectory(): void
    {
        $root = Path::findProjectRoot(__DIR__);
        
        $this->assertIsString($root);
        $this->assertFileExists($root . '/.env');
    }

    /** @covers Path::findProjectRoot */
    public function testFindProjectRootReturnsNullWhenNoEnvFile(): void
    {
        $root = Path::findProjectRoot('/tmp');
        
        $this->assertNull($root);
    }

    /** @covers Path::getAllFiles */
    public function testGetAllFilesReturnsArrayOfFiles(): void
    {
        $testDir = danupe()->path()->base() . '/danupe/core/tests/files';
        if (!is_dir($testDir)) {
            $this->markTestSkipped('Test files directory not found: ' . $testDir);
        }
        
        $files = Path::getAllFiles($testDir);
        
        $this->assertIsArray($files);
        foreach ($files as $file) {
            $this->assertFileExists($file);
            $this->assertTrue(is_file($file));
        }
    }

    /** @covers Path::getAllFiles */
    public function testGetAllFilesFromNonExistentDirectory(): void
    {    
        $files = Path::getAllFiles('/non/existent/directory');
        
        $this->assertIsArray($files);
        $this->assertEmpty($files);
    }

    /** @covers Path::base @covers Path::plugins */
    public function testStaticMethodCallsWork(): void
    {
        $base = Path::base();
        $plugins = Path::plugins();
        
        $this->assertIsString($base);
        $this->assertIsArray($plugins);
    }
}
