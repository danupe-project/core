<?php
declare(strict_types=1);

namespace Danube\Core\Test\Classes;

use Danube\Core\Classes\Env;
use Danube\Core\Classes\Path;
use Danube\Core\TestCase;


class EnvTest extends TestCase
{

    public function testEnv(): void
    {
        $this->assertFileExists(Path::base() . '/.env');
    }

    public function testGetString(): void
    {
        $path = dirname(__DIR__, 3) . '/tests/files/.env';
        Env::init($path);
        $this->assertEquals('VALUE!', Env::get('TEST_STRING'));
    }

    public function testGetArrayValues()
    {

        $path = dirname(__DIR__, 3) . '/tests/files/.env';
        Env::init($path);
        $expected = 'value1,value2';
        $this->assertEquals($expected, Env::get('TEST_ARRAY_1'));
        $this->assertEquals($expected, Env::get('TEST_ARRAY_2'));
    }

    public function testGetArray()
    {
        $path = dirname(__DIR__, 3) . '/tests/files/.env';
        Env::init($path);
        $expected = ['value1', 'value2'];
        $this->assertEquals($expected, Env::getArray('TEST_ARRAY_1'));
        $this->assertEquals($expected, Env::getArray('TEST_ARRAY_2'));
        $this->assertEquals($expected, Env::getArray('TEST_ARRAY_3'));
    }


}
