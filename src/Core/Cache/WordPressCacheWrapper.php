<?php

namespace Themosis\Core\Cache;

class WordPressCacheWrapper
{
    /**
     * Adds data to the cache if the cache key doesn't already exist.
     *
     * @param string|int $key
     * @param mixed      $data
     * @param string     $group
     * @param int        $expire
     *
     * @return bool
     */
    public function add($key, $data, $group = 'default', $expire = 0): bool
    {
    }
}
