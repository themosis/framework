<?php

/**
 * Themosis Framework
 * WordPress Object Cache Drop-In
 */
use Themosis\Core\Cache\WordPressCacheWrapper;

if (! defined('ABSPATH')) {
    exit();
}

/**
 * Sets up global object cache instance.
 * The used instance is the one defined as the default
 * cache driver from the application cache configuration.s
 *
 * @global \Illuminate\Cache\Repository $wp_object_cache
 */
function wp_cache_init()
{
    $store = app('cache.store');
    $isMultisite = is_multisite();

    $GLOBALS['wp_object_cache'] = new WordPressCacheWrapper(
        $store,
        $isMultisite,
        $isMultisite ? get_current_blog_id().':' : '',
    );
}

/**
 * Adds data to the cache if the cache key doesn't already exist.
 *
 * @param  string|int  $key
 * @param  mixed  $data
 * @param  string  $group
 * @param  int  $expire
 * @return bool True on success. False if cache value is already in the store.
 */
function wp_cache_add($key, $data, $group = '', $expire = 0)
{
    global $wp_object_cache;

    return $wp_object_cache->add($key, $data, $group, (int) $expire);
}

/**
 * Closes the cache.
 *
 * @return bool
 */
function wp_cache_close()
{
    return true;
}

/**
 * Decrement numeric cache item's value.
 *
 * @param  string|int  $key
 * @param  int  $offset
 * @param  string  $group
 * @return bool|int False on failure. The item's new value on success.
 */
function wp_cache_decr($key, $offset = 1, $group = '')
{
    global $wp_object_cache;

    return $wp_object_cache->decrement($key, $offset, $group);
}

/**
 * Increment numeric cache item's value.
 *
 * @param  string|int  $key
 * @param  int  $offset
 * @param  string  $group
 * @return bool|int False on failure. The item's new value on success.
 */
function wp_cache_incr($key, $offset = 1, $group = '')
{
    global $wp_object_cache;

    return $wp_object_cache->increment($key, $offset, $group);
}

/**
 * Removes the cache contents matching key.
 *
 * @param  string|int  $key
 * @param  string  $group
 * @return bool True on success. False on failure.
 */
function wp_cache_delete($key, $group = '')
{
    global $wp_object_cache;

    return $wp_object_cache->delete($key, $group);
}

/**
 * Removes all cache items.
 *
 * @return bool True on success. False on failure.
 */
function wp_cache_flush()
{
    global $wp_object_cache;

    return $wp_object_cache->flush();
}

/**
 * Retrieve the cache content from the cache by key.
 *
 * @param  string|int  $key
 * @param  string  $group
 * @param  bool  $force
 * @param  null  $found
 * @return mixed False on failure. Cache content on success.
 */
function wp_cache_get($key, $group = '', $force = false, &$found = null)
{
    global $wp_object_cache;

    return $wp_object_cache->get($key, $group, $force, $found);
}

/**
 * Store an item in the cache.
 *
 * @param  string|int  $key
 * @param  mixed  $data
 * @param  string  $group
 * @param  int  $expire
 * @return bool False on failure. True on success.
 */
function wp_cache_set($key, $data, $group = '', $expire = 0)
{
    global $wp_object_cache;

    return $wp_object_cache->set($key, $data, $group, (int) $expire);
}

/**
 * Replaces the content of the cache with new data.
 *
 * @param  string|int  $key
 * @param  mixed  $data
 * @param  string  $group
 * @param  int  $expire
 * @return bool False if original value does not exists. True if replaced.
 */
function wp_cache_replace($key, $data, $group = '', $expire = 0)
{
    global $wp_object_cache;

    return $wp_object_cache->replace($key, $data, $group, (int) $expire);
}

/**
 * Switches the internal blog ID (prefix).
 *
 * @param  string|int  $blog_id
 */
function wp_cache_switch_to_blog($blog_id)
{
    global $wp_object_cache;

    $wp_object_cache->switchToBlog((int) $blog_id);
}

/**
 * Adds a group or set of groups to the list of global groups.
 *
 * @param  string|array  $groups
 */
function wp_cache_add_global_groups($groups)
{
    global $wp_object_cache;

    $wp_object_cache->addGlobalGroups($groups);
}

/**
 * Adds a group or set of groups to the list of non-persistent groups.
 *
 * @param  string|array  $groups
 */
function wp_cache_add_non_persistent_groups($groups)
{
    global $wp_object_cache;

    $wp_object_cache->addNonPersistentGroups((array) $groups);
}

/**
 * Reset internal cache keys and structures.
 *
 * @return bool
 */
function wp_cache_reset()
{
    return true;
}
