<?php
declare(strict_types=1);

namespace Danupe\Core\Test\Classes;

use Danupe\Core\Classes\Env;
use Danupe\Core\Classes\Path;
use Danupe\Core\TestCase;


class EnvTest extends TestCase
{

    public function testEnv(): void
    {
        $this->assertFileExists(danupe()->path()->base() . '/.env');
    }

    public function testGetString(): void
    {
        $path = dirname(__DIR__, 3) . '/tests/files/.env';
        danupe()->env()->init($path);
        $this->assertEquals('VALUE!', danupe()->env()->get('TEST_STRING'));
    }

    public function testGetArrayValues()
    {

        $path = dirname(__DIR__, 3) . '/tests/files/.env';
        danupe()->env()->init($path);
        $expected = 'value1,value2';
        $this->assertEquals($expected, danupe()->env()->get('TEST_ARRAY_1'));
        $this->assertEquals($expected, danupe()->env()->get('TEST_ARRAY_2'));
    }

    public function testGetArray()
    {
        $path = dirname(__DIR__, 3) . '/tests/files/.env';
        danupe()->env()->init($path);
        $expected = ['value1', 'value2'];
        $this->assertEquals($expected, danupe()->env()->getArray('TEST_ARRAY_1'));
        $this->assertEquals($expected, danupe()->env()->getArray('TEST_ARRAY_2'));
        $this->assertEquals($expected, danupe()->env()->getArray('TEST_ARRAY_3'));
    }


}
