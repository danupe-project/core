<?php

declare(strict_types=1);

namespace Danupe\Core\Tests\Unit\Classes;

use Danupe\Core\Classes\Config;
use Danupe\Core\TestCase;

class ConfigTest extends TestCase
{
    private Config $config;
    private Config $configWithFiles;
    private string $testFilesPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new Config();
        $this->testFilesPath = danupe()->path()->plugin('core') . '/tests/files';
        $this->configWithFiles = new Config($this->testFilesPath);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->config, $this->configWithFiles);
    }

    /** @covers Config::all @covers Config::__construct */
    public function testAllReturnsConfigurationAsArray(): void
    {
        $allConfig = $this->configWithFiles->all();
        
        $this->assertIsArray($allConfig);
        $this->assertNotEmpty($allConfig);
    }

    /** @covers Config::get */
    public function testGetReturnsSpecificConfigValue(): void
    {
        $result = $this->configWithFiles->get('files.testkey');
        $allConfig = $this->configWithFiles->all();
        
        $this->assertIsArray($allConfig);
        
        if (is_array($result)) {
            $this->assertContains('testvalue', $result);
        } else {
            $this->assertEquals('testvalue', $result);
        }
    }

    /** @covers Config::get */
    public function testGetReturnsDefaultValueWhenKeyNotExists(): void
    {
        $result = $this->config->get('nonexistent.key', 'default');
        
        $this->assertEquals('default', $result);
    }

    /** @covers Config::set */
    public function testSetStoresConfigurationValue(): void
    {
        $this->config->set('test', 'test.value');
        
        $this->assertEquals('test.value', $this->config->get('test'));
    }

    /** @covers Config::set @covers Config::get */
    public function testSetAndGetNestedValues(): void
    {
        $testData = ['nested' => ['array' => 'value']];
        $this->config->set('complex', $testData);
        
        $this->assertEquals($testData, $this->config->get('complex'));
        $this->assertEquals('value', $this->config->get('complex.nested.array'));
    }

    /** @covers Config::getAllByKey */
    public function testGetAllByKeyReturnsFilteredConfiguration(): void
    {
        $result = $this->configWithFiles->getAllByKey('more');
        
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        
        foreach ($result as $pluginConfig) {
            $this->assertIsArray($pluginConfig);
            $this->assertArrayHasKey('testkey', $pluginConfig);
        }
    }

    /** @covers Config::getAllByKey */
    public function testGetAllByKeyAsOneDimensionalArray(): void
    {
        $result = $this->configWithFiles->getAllByKey('more', true);
        
        $this->assertIsArray($result);
        $this->assertIsArray(current($result));
    }

    /** @covers Config::all */
    public function testEmptyConfigurationReturnsEmptyArray(): void
    {
        $emptyConfig = new Config('');
        $result = $emptyConfig->all();
        
        $this->assertIsArray($result);
    }

    /** @covers Config::get */
    public function testGetDeeplyNestedConfigurationValue(): void
    {
        $nestedData = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'value' => 'deep_value'
                    ]
                ]
            ]
        ];
        $this->config->set('nested_config', $nestedData);
        
        $this->assertEquals('deep_value', $this->config->get('nested_config.level1.level2.level3.value'));
    }

    /** @covers Config::__construct */
    public function testConfigurationWithArrayOfPaths(): void
    {
        $paths = [$this->testFilesPath];
        $config = new Config($paths);
        
        $this->assertIsArray($config->all());
        $this->assertNotEmpty($config->all());
    }
}
