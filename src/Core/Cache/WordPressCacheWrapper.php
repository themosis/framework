<?php

namespace Themosis\Core\Cache;

use Illuminate\Contracts\Cache\Repository;

class WordPressCacheWrapper
{
    /**
     * Cache store instance.
     *
     * @var Repository
     */
    private $store;

    /**
     * Global cache groups.
     *
     * @var array
     */
    private $globalGroups = [];

    /**
     * Global non persistent groups.
     *
     * @var array
     */
    private $nonPersistentGroups = [];

    /**
     * Blog ID prefix followed by a colon ":"
     *
     * @var string "$id:"
     */
    private $blogPrefix;

    /**
     * Is is a multisite installation.
     *
     * @var bool
     */
    private $multisite;

    /**
     * Default group name if none defined.
     *
     * @var string
     */
    private $defaultGroup = 'default';

    public function __construct(Repository $store, bool $multisite = false, string $blogPrefix = '')
    {
        $this->store = $store;
        $this->multisite = $multisite;
        $this->blogPrefix = $blogPrefix;
    }

    /**
     * Sets the list of global cache groups.
     */
    public function addGlobalGroups(array $groups)
    {
        $groups = array_fill_keys($groups, true);

        $this->globalGroups = array_merge(
            $this->globalGroups,
            $groups,
        );
    }

    /**
     * Adds a group or set of groups to the list of non-persistent groups.
     */
    public function addNonPersistentGroups(array $groups)
    {
        $this->nonPersistentGroups = array_unique(
            array_merge(
                $this->nonPersistentGroups,
                $groups,
            ),
        );
    }

    /**
     * Switches the internal blog prefix ID.
     */
    public function switchToBlog(int $blog_id)
    {
        $this->blogPrefix = $this->multisite ? $blog_id.':' : '';
    }

    /**
     * Format key name based on a key and a group.
     * WordPress cache keys are stored using a nomenclature
     * in their name: groupname_keyname
     *
     *
     * @return string
     */
    private function formatKeyName(string $key, string $group)
    {
        return sprintf('%s_%s', $group, $key);
    }

    /**
     * Retrieves the cache contents, it it exists.
     *
     * @param  string|int  $key
     * @param  string  $group
     * @param  bool  $force
     * @param  null  $found
     * @return bool|mixed False on failure. Cache value on success.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get($key, $group = 'default', $force = false, &$found = null)
    {
        if (empty($group)) {
            $group = $this->defaultGroup;
        }

        if ($this->multisite && ! isset($this->globalGroups[$group])) {
            $key = $this->blogPrefix.$key;
        }

        $key = $this->formatKeyName($key, $group);

        if ($this->store->has($key)) {
            $found = true;

            return $this->store->get($key);
        }

        return false;
    }

    /**
     * Store an item into the cache.
     *
     * @param  string|int  $key
     * @param  mixed  $data
     * @param  string  $group
     * @param  int  $expire
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set($key, $data, $group = 'default', $expire = 0)
    {
        if (empty($group)) {
            $group = $this->defaultGroup;
        }

        if ($this->multisite && ! isset($this->globalGroups[$group])) {
            $key = $this->blogPrefix.$key;
        }

        $key = $this->formatKeyName($key, $group);

        return $this->store->set($key, $data, $expire);
    }

    /**
     * Adds data to the cache if the cache key doesn't already exist.
     *
     * @param  string|int  $key
     * @param  mixed  $data
     * @param  string  $group
     * @param  int  $expire
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function add($key, $data, $group = 'default', $expire = 0): bool
    {
        if (function_exists('wp_suspend_cache_addition') && wp_suspend_cache_addition()) {
            return false;
        }

        if (empty($group)) {
            $group = $this->defaultGroup;
        }

        // We must preserve the original key name as the set method will
        // format the key name as well.
        $id = $key;

        if ($this->multisite && ! isset($this->globalGroups[$group])) {
            $id = $this->blogPrefix.$key;
        }

        $id = $this->formatKeyName($id, $group);

        if ($this->store->has($id)) {
            return false;
        }

        return $this->set($key, $data, $group, (int) $expire);
    }

    /**
     * Decrement numeric cache item's value.
     *
     * @param  string|int  $key
     * @param  int  $offset
     * @param  string  $group
     * @return bool|int
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function decrement($key, $offset = 1, $group = 'default')
    {
        if (empty($group)) {
            $group = $this->defaultGroup;
        }

        if ($this->multisite && ! isset($this->globalGroups[$group])) {
            $key = $this->blogPrefix.$key;
        }

        $key = $this->formatKeyName($key, $group);

        if (! $this->store->has($key)) {
            return false;
        }

        return $this->store->decrement($key, $offset);
    }

    /**
     * Increment numeric cache item's value.
     *
     * @param  string|int  $key
     * @param  int  $offset
     * @param  string  $group
     * @return bool|int
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function increment($key, $offset = 1, $group = 'default')
    {
        if (empty($group)) {
            $group = $this->defaultGroup;
        }

        if ($this->multisite && ! isset($this->globalGroups[$group])) {
            $key = $this->blogPrefix.$key;
        }

        $key = $this->formatKeyName($key, $group);

        if (! $this->store->has($key)) {
            return false;
        }

        return $this->store->increment($key, $offset);
    }

    /**
     * Removes the cache contents matching key.
     *
     * @param  string|int  $key
     * @param  string  $group
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete($key, $group = 'default')
    {
        if (empty($group)) {
            $group = $this->defaultGroup;
        }

        if ($this->multisite && ! isset($this->globalGroups[$group])) {
            $key = $this->blogPrefix.$key;
        }

        $key = $this->formatKeyName($key, $group);

        return $this->store->delete($key);
    }

    /**
     * Replaces the content in the cache, if content already exists.
     *
     * @param  string|int  $key
     * @param  mixed  $data
     * @param  string  $group
     * @param  int  $expire
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function replace($key, $data, $group = 'default', $expire = 0)
    {
        if (empty($group)) {
            $group = $this->defaultGroup;
        }

        $id = $key;

        if ($this->multisite && ! isset($this->globalGroups[$group])) {
            $id = $this->blogPrefix.$key;
        }

        $id = $this->formatKeyName($id, $group);

        if (! $this->store->has($id)) {
            return false;
        }

        return $this->set($key, $data, $group, (int) $expire);
    }

    /**
     * Removes all cache items.
     *
     * @return bool
     */
    public function flush()
    {
        return $this->store->clear();
    }
}
