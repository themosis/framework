<?php

/**
 * Themosis Framework
 * WordPress Object Cache Drop-In
 */

use Illuminate\Support\Facades\Cache;

if (! defined('ABSPATH')) {
    die();
}

Cache::put('wp', 'test', 3600);
