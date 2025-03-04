<?php

use Danube\Core\Classes\Path;
use PHPUnit\Framework\TestCase;
use Danube\Core\Classes\Config;
class ConfigTest extends TestCase
{

    public function testGetConfig()
    {
        $config = new Config(Path::plugin('core').'/tests/files');
        $this->assertIsArray($config->all());
        $this->assertEquals('testvalue', $config->get('files.testkey'));
    }
    public function testSetConfig()
    {
        $config = new Config();
        $config->set('test', 'test');
        $this->assertEquals('test', $config->get('test'));
    }

}
