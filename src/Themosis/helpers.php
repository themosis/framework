<?php

use Themosis\Foundation\Application;

/*
 * Helpers functions globally available.
 */
if (!function_exists('themosis_is_subpage')) {
    /**
     * Define if the current page is a child page.
     *
     * @param array $parent The parent page properties.
     *
     * @return int|bool Parent page ID if subpage. False if not.
     */
    function themosis_is_subpage(array $parent)
    {
        global $post;

        $parentPage = get_post($post->post_parent);

        if (is_page() && $post->post_parent && $parentPage->post_name === $parent[0]) {
            return $post->post_parent;
        }

        return false;
    }
}

if (!function_exists('themosis_convert_path')) {
    /**
     * Convert '.' into '/' directory separators.
     *
     * @param string $path The initial path with '.'
     *
     * @return string The converted path with '/'
     */
    function themosis_convert_path($path)
    {
        if (strpos($path, '.') !== false) {
            $path = str_replace('.', DS, $path);
        } else {
            $path = trim($path);
        }

        return (string) $path;
    }
}

if (!function_exists('td')) {
    /**
     * Print and die a value - Used for debugging.
     *
     * @param mixed $value Any PHP value.
     */
    function td($value)
    {
        $attributes = func_get_args();
        if (count($attributes) == 1) {
            $attributes = $attributes[0];
        }
        echo '<pre>';
        print_r($attributes);
        echo '</pre>';
        wp_die();
    }
}

if (!function_exists('tp')) {
    /**
     * Print a value.
     *
     * @param mixed $value Any PHP value
     */
    function tp($value)
    {
        $attributes = func_get_args();
        if (count($attributes) == 1) {
            $attributes = $attributes[0];
        }
        echo '<pre>';
        print_r($attributes);
        echo '</pre>';
    }
}

if (!function_exists('themosis_assets')) {
    /**
     * Return the application front-end assets directory URL.
     *
     * @return string
     */
    function themosis_assets()
    {
        // Check if the theme helper function exists.
        // Only if a themosis-theme is used.
        if (function_exists('themosis_theme_assets')) {
            return themosis_theme_assets();
        }

        return get_template_directory_uri().'/resources/assets';
    }
}

if (!function_exists('themosis_get_the_query')) {
    /**
     * Return the WP Query variable.
     *
     * @return object The global WP_Query instance.
     */
    function themosis_get_the_query()
    {
        global $wp_query;

        return $wp_query;
    }
}

if (!function_exists('themosis_use_permalink')) {
    /**
     * Conditional function that checks if WP
     * is using a pretty permalink structure.
     *
     * @return bool True. False if not using permalink.
     */
    function themosis_use_permalink()
    {
        global $wp_rewrite;

        if (!$wp_rewrite->permalink_structure == '') {
            return true;
        }

        return false;
    }
}

if (!function_exists('themosis_add_filters')) {
    /**
     * Helper that runs multiple add_filter
     * functions at once.
     *
     * @param array  $tags     Filter tags.
     * @param string $function The name of the global function to call.
     */
    function themosis_add_filters(array $tags, $function)
    {
        foreach ($tags as $tag) {
            add_filter($tag, $function);
        }
    }
}

if (!function_exists('themosis_get_post_id')) {
    /**
     * A function that retrieves the post ID during
     * a wp-admin request on posts and custom post types.
     *
     * @return int|null
     */
    function themosis_get_post_id()
    {
        $id = null;

        // When viewing the cpt (GET)
        if (isset($_GET['post'])) {
            $id = $_GET['post'];
        }

        // When saving the cpt (POST)
        if (isset($_POST['post_ID'])) {
            $id = $_POST['post_ID'];
        }

        return $id;
    }
}

if (!function_exists('themosis_is_post')) {
    /**
     * A function that checks you're on a specified
     * admin page, post, or custom post type (edit) in order to display
     * a certain content.
     *
     * Example : Place a specific metabox for a page, a post or a one of your
     * custom post type.
     *
     * Give the post ID. Visible in the admin uri in your browser.
     *
     * @param int $id A WP_Post ID
     *
     * @return bool True. False if not a WordPress post type.
     */
    function themosis_is_post($id)
    {
        $postId = themosis_get_post_id();

        if (!is_null($postId) && is_numeric($id) && $id === (int) $postId) {
            return true;
        }

        return false;
    }
}

if (!function_exists('themosis_attachment_id_from_url')) {
    /**
     * A function that returns the 'attachment_id' of a
     * media file by giving its URL.
     *
     * @param string $url The media/image URL - Works only for images uploaded from within WordPress.
     *
     * @return int|bool The image/attachment_id if it exists, false if not.
     */
    function themosis_attachment_id_from_url($url = null)
    {
        /*-----------------------------------------------------------------------*/
        // Load the DB class
        /*-----------------------------------------------------------------------*/
        global $wpdb;

        /*-----------------------------------------------------------------------*/
        // Set attachment_id
        /*-----------------------------------------------------------------------*/
        $id = false;

        /*-----------------------------------------------------------------------*/
        // If there is no url, return.
        /*-----------------------------------------------------------------------*/
        if (null === $url) {
            return;
        }

        /*-----------------------------------------------------------------------*/
        // Get the upload directory paths
        /*-----------------------------------------------------------------------*/
        $upload_dir_paths = wp_upload_dir();

        /*-----------------------------------------------------------------------*/
        // Make sure the upload path base directory exists in the attachment URL,
        // to verify that we're working with a media library image
        /*-----------------------------------------------------------------------*/
        if (false !== strpos($url, $upload_dir_paths['baseurl'])) {
            /*-----------------------------------------------------------------------*/
            // If this is the URL of an auto-generated thumbnail,
            // get the URL of the original image
            /*-----------------------------------------------------------------------*/
            $url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $url);

            /*-----------------------------------------------------------------------*/
            // Remove the upload path base directory from the attachment URL
            /*-----------------------------------------------------------------------*/
            $url = str_replace($upload_dir_paths['baseurl'].'/', '', $url);

            /*-----------------------------------------------------------------------*/
            // Grab the database prefix
            /*-----------------------------------------------------------------------*/
            $prefix = $wpdb->prefix;

            /*-----------------------------------------------------------------------*/
            // Finally, run a custom database query to get the attachment ID
            // from the modified attachment URL
            /*-----------------------------------------------------------------------*/
            $id = $wpdb->get_var($wpdb->prepare("SELECT {$prefix}posts.ID FROM $wpdb->posts {$prefix}posts, $wpdb->postmeta {$prefix}postmeta WHERE {$prefix}posts.ID = {$prefix}postmeta.post_id AND {$prefix}postmeta.meta_key = '_wp_attached_file' AND {$prefix}postmeta.meta_value = '%s' AND {$prefix}posts.post_type = 'attachment'", $url));
        }

        return $id;
    }
}

if (!function_exists('themosis_is_template')) {
    /**
     * A function that checks if we are using a page template.
     *
     * @param array $name Template properties.
     *
     * @return bool True: use of a template. False: no template.
     */
    function themosis_is_template(array $name = [])
    {
        $queriedObject = get_queried_object();

        if (is_a($queriedObject, 'WP_Post') && 'page' === $queriedObject->post_type) {
            // Sanitized value
            $template = Meta::get($queriedObject->ID, '_themosisPageTemplate');

            // If no template selected, just return;
            if ($template === 'none') {
                return false;
            }

            // If template...
            if (isset($template) && !empty($template)) {
                /*-----------------------------------------------------------------------*/
                // If the page template name is defined within the routes array, handle
                // the template
                /*-----------------------------------------------------------------------*/
                if (in_array($template, $name)) {
                    return true;
                }
            }

            return false;
        }
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     *
     * @param string $value
     *
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    function starts_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_set')) {
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (!function_exists('array_except')) {
    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param array $array
     * @param array $keys
     *
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array) $keys));
    }
}

if (!function_exists('array_is_sequential')) {
    /**
     * Check if an array is sequential (have keys from 0 to n) or not.
     *
     * @param array $array The array to check.
     *
     * @return bool
     */
    function array_is_sequential($array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('with')) {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param mixed $object
     *
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}

if (!function_exists('str_contains')) {
    /**
     * Determine if a given string contains a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('app')) {
    /**
     * Helper function to quickly retrieve an instance.
     *
     * @param null  $abstract   The abstract instance name.
     * @param array $parameters
     *
     * @return mixed
     */
    function app($abstract = null, array $parameters = [])
    {
        $application = isset($GLOBALS['themosis']) ? $GLOBALS['themosis']->container : Application::getInstance();

        if (is_null($abstract)) {
            return $application;
        }

        return $application->make($abstract, $parameters);
    }
}

if (!function_exists('container')) {
    /**
     * Helper function to quickly retrieve an instance.
     *
     * @param null  $abstract   The abstract instance name.
     * @param array $parameters
     *
     * @return mixed
     */
    function container($abstract = null, array $parameters = [])
    {
        return app($abstract, $parameters);
    }
}

if (!function_exists('themosis')) {
    /**
     * Helper function to retrieve the Themosis class instance.
     * 
     * @return Themosis
     */
    function themosis()
    {
        if (!class_exists('Themosis')) {
            wp_die('Themosis has not yet been initialized. Please make sure the Themosis framework is installed.');
        }

        return Themosis::instance();
    }
}

if (!function_exists('view')) {
    /**
     * Helper function to build views.
     *
     * @param string $view      The view relative path, name.
     * @param array  $data      Passed data.
     * @param array  $mergeData
     *
     * @return string
     */
    function view($view = null, array $data = [], array $mergeData = [])
    {
        $factory = container('view');

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData)->render();
    }
}

if (!function_exists('meta')) {
    /**
     * Helper function to get any meta data from objects.
     *
     * @param string $key
     * @param int    $id
     * @param string $context
     * @param bool   $single
     *
     * @return mixed|string
     */
    function meta($key = '', $id = null, $context = 'post', $single = true)
    {
        if (is_null($id)) {
            $id = get_the_ID();
        }

        // If no ID found, return empty string.
        if (!$id) {
            return '';
        }

        return get_metadata($context, $id, $key, $single);
    }
}
