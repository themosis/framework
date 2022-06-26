<?php

/*
|--------------------------------------------------------------------------
| Hook - Test functions
|--------------------------------------------------------------------------
|
*/
function actionHookCallback()
{
}

function callingForUncharted()
{
}

/*
|--------------------------------------------------------------------------
| Routing - Test functions
|--------------------------------------------------------------------------
|
*/
global $post, $wp_query;

function is_home()
{
    return true;
}

/**
 * Detect if is a WordPress page.
 *
 * @param  string|int|array  $slug
 * @return bool
 */
function is_page($slug = null)
{
    if (is_numeric($slug) && 30 === $slug) {
        return true;
    } elseif (is_numeric($slug)) {
        return false;
    }

    if (is_string($slug) && 'contact' === $slug) {
        return true;
    } elseif (is_string($slug)) {
        return false;
    }

    return true;
}

function is_404()
{
    return true;
}

function is_archive()
{
    return true;
}

function is_attachment()
{
    return true;
}

function is_author()
{
    return true;
}

function is_category($category = null)
{
    if (is_numeric($category) && 20 === $category) {
        return true;
    } elseif (is_numeric($category)) {
        return false;
    }

    if (is_string($category) && 'featured' === $category) {
        return true;
    } elseif (is_string($category)) {
        return false;
    }

    return true;
}

function is_date()
{
    return true;
}

function is_day()
{
    return true;
}

function is_front_page()
{
    return true;
}

function is_month()
{
    return true;
}

function is_paged()
{
    return true;
}

function is_page_template($mixed = null)
{
    if (is_string($mixed) && 'about' === $mixed) {
        return true;
    } elseif (is_string($mixed)) {
        return false;
    }

    return true;
}

function is_post_type_archive($mixed = null)
{
    if (is_string($mixed) && 'events' === $mixed) {
        return true;
    } elseif (is_string($mixed)) {
        return false;
    } elseif (is_array($mixed) && ['services', 'books'] === $mixed) {
        return true;
    } elseif (is_array($mixed)) {
        return false;
    }

    return true;
}

function is_search()
{
    return true;
}

function is_single()
{
    return true;
}

function is_singular($mixed = null)
{
    if (is_string($mixed) && 'books' === $mixed) {
        return true;
    } elseif (is_string($mixed)) {
        return false;
    }

    if (is_array($mixed) && ['events', 'services'] === $mixed) {
        return true;
    } elseif (is_array($mixed)) {
        return false;
    }

    return true;
}

function is_sticky()
{
    return true;
}

function is_tag()
{
    return true;
}

function is_tax()
{
    return true;
}

function is_time()
{
    return true;
}

function is_year()
{
    return true;
}

function is_custom($num)
{
    if (42 === $num) {
        return true;
    }

    return false;
}

function wp_get_theme()
{
    $theme = new stdClass();
    $theme->stylesheet = 'underscore';

    return $theme;
}

function get_home_url($blog_id = null, $path = '')
{
    $url = 'http://example.com';
    if ($path && is_string($path)) {
        $url .= '/'.ltrim($path, '/');
    }

    return $url;
}

/*
|--------------------------------------------------------------------------
| Callback Handler
|--------------------------------------------------------------------------
|
*/
function themosis_callback_helper($args = [])
{
    if (! empty($args) && isset($args['params'])) {
        return $args['params'];
    }

    return true;
}
