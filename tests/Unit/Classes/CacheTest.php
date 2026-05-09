<?php

namespace Tests\Core;

use PHPUnit\Framework\TestCase;
use Danupe\Core\Classes\Cache;

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = new Cache();
    }

    public function testGetNonExistentKeyReturnsNull()
    {
        $this->assertNull($this->cache->get('non_existent_key'));
    }

    public function testSetAndGetKey()
    {
        $key = 'test_key';
        $value = 'test_value';
        $this->cache->set($key, $value);
        $this->assertEquals($value, $this->cache->get($key));
    }

    public function testHasKeyReturnsTrueAfterSet()
    {
        $key = 'test_key';
        $this->cache->set($key, 'test_value');
        $this->assertTrue($this->cache->has($key));
    }

    public function testDeleteKeyRemovesEntry()
    {
        $key = 'test_key';
        $this->cache->set($key, 'test_value');
        $this->cache->delete($key);
        $this->assertFalse($this->cache->has($key));
    }

    public function testTtlExpiresCacheItem()
    {
        $key = 'ttl_key';
        $value = 'ttl_value';
        $ttl = 1; // 1 second
        $this->cache->set($key, $value, $ttl);
        sleep(2); // Wait for TTL to expire
        $this->assertFalse($this->cache->has($key));
    }
}