<?php

use Danupe\Core\Classes\Path;
use Danupe\Core\TestCase;
use Danupe\Core\Classes\Config;
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
