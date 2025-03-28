<?php

use Danupe\Core\Classes\Path;
use Danupe\Core\TestCase;
use Danupe\Core\Classes\Config;
class ConfigTest extends TestCase
{

    public function testGetConfig()
    {
        $config = new Config(danupe()->path()->plugin('core').'/tests/files');
        $this->assertIsArray($config->all());
        $this->assertEquals(['testvalue','testvalue2'], $config->get('files.testkey'));
    }
    public function testSetConfig()
    {
        $config = new Config();
        $config->set('test', 'test');
        $this->assertEquals('test', $config->get('test'));
    }

    public function testGetAllByKey(){
        $config = new Config(danupe()->path()->plugin('core').'/tests/files');
        $result = $config->getAllByKey('more');
        $result = danupe()->data()->get($result,'0');
        $this->assertIsArray($result);
        $this->assertCount(2, danupe()->data()->get($result,'testkey'));

    }


public function testGetRoutes(){
    $config = new Config(danupe()->path()->plugin('core').'/tests/files');
    $result = $config->getRoutes();
    $this->assertIsArray($result['guest']);
    $this->assertCount(2, $result['guest']['GET']);
    $this->assertCount(1, $result['guest']['POST']);
}

}
