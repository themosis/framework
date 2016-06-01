<?php

namespace Themosis\Metabox;

class Meta
{
    /**
     * Retrieve the meta data from the given
     * post type ID and meta key.
     * 
     * @param int    $id     The post type ID.
     * @param string $key    The meta name.
     * @param bool   $single Default to true. False to return as an array.
     *
     * @return mixed The meta value.
     */
    public static function get($id, $key = '', $single = true)
    {
        $default = get_post_meta($id, $key, $single);

        if (is_ssl()) {
            return static::parse($default);
        }

        return $default;
    }

    /**
     * Parse the returned value by 'get_post_meta'. Convert
     * URLs from the domain to their 'https' equivalent.
     * Avoid getting an alert message for non-secure asset in browsers.
     * 
     * @param mixed $default The default value returned by 'get_post_meta'. Could be of type: string, boolean, int, array,...
     *
     * @return mixed The converted value if needed or the default one.
     */
    private static function parse($default)
    {
        /*-----------------------------------------------------------------*/
        // Use of recursive function if $default is an array.
        // Check all values for arrays and their children arrays...
        /*-----------------------------------------------------------------*/
        if (is_array($default)) {
            foreach ($default as $key => $value) {
                $default[$key] = static::parse($value);
            }

            return $default;
        }

        /*-----------------------------------------------------------------*/
        // Check if we're dealing with an URL.  If so, set the correct
        // http scheme. Return the $value if corrects or FALSE when it's not
        /*-----------------------------------------------------------------*/
        $value = filter_var($default, FILTER_VALIDATE_URL);

        if (!$value) {

            /*-----------------------------------------------------------------*/
            // This is not a valid URL - Return the default one
            /*-----------------------------------------------------------------*/
            return $default;
        } else {
            if (0 === strpos($value, 'http') && 0 !== strpos($value, 'https')) {
                if (static::isFromDomain($value)) {
                    $count = 1; // 4th parameter has to be a variable
                    $value = str_replace('http', 'https', $value, $count);

                    return $value;
                }
            }
        }

        return $default;
    }

    /**
     * Determine if the given URL belong to the domain.
     * Tell if the URL is internal and not pointing to
     * an external domain.
     * 
     * @param string $url The URL from a custom field
     *
     * @return bool True if application URL, false if external.
     */
    private static function isFromDomain($url)
    {
        $home_url = home_url();
        $home_url = parse_url($home_url);

        $url = parse_url($url);

        if ($url['host'] === $home_url['host']) {
            return true;
        }

        return false;
    }
}
