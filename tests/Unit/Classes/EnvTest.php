<?php

declare(strict_types=1);

namespace Danupe\Core\Tests\Unit\Classes;

use Danupe\Core\Classes\Env;
use Danupe\Core\TestCase;
use RuntimeException;

class EnvTest extends TestCase
{
    private string $testEnvPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testEnvPath = dirname(__DIR__, 3) . '/tests/files/.env';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($_ENV['TEST_STRING'], $_ENV['TEST_ARRAY_1'], $_ENV['TEST_ARRAY_2'], $_ENV['TEST_ARRAY_3']);
        unset($_SERVER['TEST_STRING'], $_SERVER['TEST_ARRAY_1'], $_SERVER['TEST_ARRAY_2'], $_SERVER['TEST_ARRAY_3']);
    }

    /** @covers Env::__construct */
    public function testEnvFileExistsInProjectRoot(): void
    {
        $this->assertFileExists(danupe()->path()->base() . '/.env');
    }

    /** @covers Env::get @covers Env::init */
    public function testGetStringValueFromEnvironment(): void
    {
        danupe()->env()->init($this->testEnvPath);
        
        $result = danupe()->env()->get('TEST_STRING');
        
        $this->assertEquals('VALUE!', $result);
        $this->assertIsString($result);
    }

    /** @covers Env::get */
    public function testGetStringValueWithDefault(): void
    {
        $result = danupe()->env()->get('NON_EXISTENT_KEY', 'default_value');
        
        $this->assertEquals('default_value', $result);
    }

    /** @covers Env::get */
    public function testGetStringValueReturnsNullWhenNotFound(): void
    {
        $result = danupe()->env()->get('NON_EXISTENT_KEY');
        
        $this->assertNull($result);
    }

    /** @covers Env::get */
    public function testGetArrayValuesAsStrings(): void
    {
        danupe()->env()->init($this->testEnvPath);
        
        $expected = 'value1,value2';
        
        $this->assertEquals($expected, danupe()->env()->get('TEST_ARRAY_1'));
        $this->assertEquals($expected, danupe()->env()->get('TEST_ARRAY_2'));
    }

    /** @covers Env::getArray */
    public function testGetArrayValuesAsArrays(): void
    {
        danupe()->env()->init($this->testEnvPath);
        
        $expected = ['value1', 'value2'];
        
        $this->assertEquals($expected, danupe()->env()->getArray('TEST_ARRAY_1'));
        $this->assertEquals($expected, danupe()->env()->getArray('TEST_ARRAY_2'));
        $this->assertEquals($expected, danupe()->env()->getArray('TEST_ARRAY_3'));
    }

    /** @covers Env::getArray */
    public function testGetArrayWithCustomDelimiter(): void
    {
        $_ENV['TEST_SEMICOLON'] = 'value1;value2;value3';
        
        $result = danupe()->env()->getArray('TEST_SEMICOLON', [], ';');
        
        $this->assertEquals(['value1', 'value2', 'value3'], $result);
    }

    /** @covers Env::getArray */
    public function testGetArrayReturnsDefaultWhenKeyNotFound(): void
    {
        $default = ['default1', 'default2'];
        $result = danupe()->env()->getArray('NON_EXISTENT_ARRAY', $default);
        
        $this->assertEquals($default, $result);
    }

    /** @covers Env::getArray */
    public function testGetArrayReturnsEmptyArrayByDefault(): void
    {
        $result = danupe()->env()->getArray('NON_EXISTENT_ARRAY');
        
        $this->assertEquals([], $result);
        $this->assertIsArray($result);
    }

    /** @covers Env::init */
    public function testInitWithNonExistentFileThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Env file not found: /non/existent/path/.env');
        
        Env::init('/non/existent/path/.env');
    }

    /** @covers Env::init */
    public function testInitWithDefaultPath(): void
    {
        $this->expectNotToPerformAssertions();
        
        Env::init();
    }

    /** @covers Env::get @covers Env::getArray */
    public function testStaticMethodCallsWork(): void
    {
        danupe()->env()->init($this->testEnvPath);
        
        $stringValue = Env::get('TEST_STRING');
        $arrayValue = Env::getArray('TEST_ARRAY_1');
        
        $this->assertEquals('VALUE!', $stringValue);
        $this->assertEquals(['value1', 'value2'], $arrayValue);
    }

    /** @covers Env::init @covers Env::get */
    public function testEnvironmentVariablesAreTrimmed(): void
    {
        $tempContent = "TEST_SPACES= spaced value \nTEST_QUOTES=\"quoted value\"";
        $tempFile = sys_get_temp_dir() . '/test_env_' . uniqid();
        file_put_contents($tempFile, $tempContent);
        
        try {
            Env::init($tempFile);
            
            $this->assertEquals('spaced value', Env::get('TEST_SPACES'));
            $this->assertEquals('quoted value', Env::get('TEST_QUOTES'));
        } finally {
            unlink($tempFile);
        }
    }

    /** @covers Env::init */
    public function testCommentsAreIgnoredInEnvFiles(): void
    {
        $tempContent = "# This is a comment\nTEST_VALUE=actual_value\n# Another comment";
        $tempFile = sys_get_temp_dir() . '/test_env_comments_' . uniqid();
        file_put_contents($tempFile, $tempContent);
        
        try {
            Env::init($tempFile);
            
            $this->assertEquals('actual_value', Env::get('TEST_VALUE'));
            $this->assertNull(Env::get('# This is a comment'));
        } finally {
            unlink($tempFile);
        }
    }
}
