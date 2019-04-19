<?php

namespace Themosis\Tests\Core;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

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
}
