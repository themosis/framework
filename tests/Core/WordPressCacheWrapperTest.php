<?php

namespace Themosis\Tests\Core;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Cache\WordPressCacheWrapper;

class WordPressCacheWrapperTest extends TestCase
{
    /**
     * Return a cache repository instance
     * with the File cache store.
     *
     * @return Repository
     */
    public function getCacheStore()
    {
        return new Repository(new FileStore(new Filesystem(), __DIR__.'/../storage/cache'));
    }

    public function test_wrapper_can_cache_items_using_default_group()
    {
        $cache = new WordPressCacheWrapper($this->getCacheStore());

        $cache->set('toto', 'blue', '', 100);

        $this->assertEquals('blue', $cache->get('toto'));
    }

    public function test_wrapper_cannot_add_items_if_exist()
    {
        $cache = new WordPressCacheWrapper($this->getCacheStore());

        $cache->set('socrate', 'nothing', 'options', 100);

        $this->assertFalse($cache->add('socrate', 'everything', 'options'));
    }

    public function test_wrapper_can_flush_cache()
    {
        $cache = new WordPressCacheWrapper($this->getCacheStore());

        $cache->set('foo', 'bar', 'plugin', 100);
        $cache->set('baz', 'zoo', 'plugin', 100);

        $cache->flush();

        $this->assertFalse($cache->get('foo', 'plugin'));
    }
}
