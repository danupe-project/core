<?php

declare(strict_types=1);

namespace Danupe\Core\Tests\Unit\Classes;

use Danupe\Core\Classes\Helper;
use Danupe\Core\TestCase;

class HelperTest extends TestCase
{
    private Helper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new Helper();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->helper);
    }

    /** @covers Helper::hello */
    public function testHelloReturnsGreeting(): void
    {
        $result = $this->helper->hello();
        
        $this->assertEquals('Hello World', $result);
        $this->assertIsString($result);
    }

    /** @covers getNamespaceFromPath */
    public function testGetNamespaceFromPluginPath(): void
    {
        $result = getNamespaceFromPath('plugin-text');
        
        $this->assertEquals('Danupe\Plugin\Text', $result);
    }

    /** @covers getNamespaceFromPath */
    public function testGetNamespaceFromProjectPath(): void
    {
        $result = getNamespaceFromPath('project-homepage');
        
        $this->assertEquals('Danupe\Project\Homepage', $result);
    }

    /** @covers getNamespaceFromPath */
    public function testGetNamespaceFromComplexPluginPath(): void
    {
        $result = getNamespaceFromPath('plugin-user-management');
        
        $this->assertEquals('Danupe\Plugin\UserManagement', $result);
    }

    /** @covers getNamespaceFromPath */
    public function testGetNamespaceFromPathWithUnderscores(): void
    {
        $result = getNamespaceFromPath('plugin-text_editor');
        
        $this->assertEquals('Danupe\Plugin\TextEditor', $result);
    }

    /** @covers getNamespaceFromPath */
    public function testGetNamespaceFromProjectPathWithDashes(): void
    {
        $result = getNamespaceFromPath('project-admin-panel');
        
        $this->assertEquals('Danupe\Project\AdminPanel', $result);
    }

    /** @covers getNamespaceFromPath */
    public function testGetNamespaceFromSimplePath(): void
    {
        $result = getNamespaceFromPath('simple-module');
        
        $this->assertEquals('Danupe\SimpleModule', $result);
    }

    /** @covers Helper::__construct */
    public function testHelperCanBeInstantiated(): void
    {
        $helper = new Helper();
        
        $this->assertInstanceOf(Helper::class, $helper);
    }
}
