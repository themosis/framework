<?php

namespace Themosis\Core\Support;

trait WordPressUrl
{
    /**
     * Format the URL. If the URL is missing the WordPress directory
     * fragment, it adds it before the common delimiter.
     *
     * @param string $url
     * @param string $delimiter
     * @param string $fragment
     *
     * @return string
     */
    public function formatUrl(string $url, string $delimiter = 'wp-admin', string $fragment = 'cms')
    {
        /*
         * If there is already a "cms" fragment in the URI,
         * just return the URL.
         */
        if (strrpos($url, $fragment) !== false) {
            return $url;
        }

        /*
         * The network admin URL is missing the "cms" fragment.
         * Let's add it.
         */
        $fragments = explode($delimiter, $url);

        /*
         * Insert in the middle the cms fragment appended with the wp-admin delimiter.
         */
        array_splice($fragments, 1, 0, "{$fragment}/{$delimiter}");

        /*
         * Build the URL by reducing (concatenating) all fragments.
         * This appends each fragment to the previous one.
         */
        $url = array_reduce($fragments, function ($carry, $item) {
            return $carry.=$item;
        });

        return $url;
    }
    
    /**
     * Format the URL for multisite purposes. If the URL is missing
     * the WordPress directory fragment, it adds it before the common delimiter.
     *
     * @param string $url
     * @param bool   $contains  If the URL should contain the fragment.
     * @param string $delimiter
     * @param string $fragment
     *
     * @return string
     */
    public function fixMultisiteUrl(string $url, bool $contains = true, ?string $delimiter = null, string $fragment = 'cms')
    {
        /*
         * Ensure that the home URL does not contain the /cms subdirectory.
         */
        if ($contains === false) {
            if (substr($url, -3) === $fragment) {
                $url = substr($url, 0, -3);
                $url = rtrim($url, '/');
            }

            return $url;
        }

        /*
         * All URL past this point should contain the cms subdirectory.
         * Let's check if the URL already contains it. If so, return the URL.
         */
        if (strrpos($url, $fragment) !== false) {
            return $url;
        }

        /*
         * If a delimiter has been passed, for example 'wp-admin,
         * it means that the fragment needs to be appended in the URL somewhere.
         */
        if ($delimiter !== null) {
            /*
             * The network site URL is missing the "cms" fragment.
             * Let's add it.
             */
            $fragments = explode($delimiter, $url);

            /*
             * Insert the cms fragment appended with the wp-admin delimiter in the middle.
             */
            array_splice($fragments, 1, 0, "{$fragment}/{$delimiter}");

            /*
             * Build the URL by imploding (concatenating) all fragments.
             */
            $url = implode('', $fragments);

            return $url;
        }

        /*
         * If the URL does not contain the fragment,
         * append it with a forward (if not inside the fragment) to the URL,
         * but only if the current site is the main site or a subdomain site.
         */
        if (substr($url, -3) !== $fragment && (is_main_site() || is_subdomain_install())) {
            if (strpos($fragment, '/') === false) {
                $fragment = '/' . $fragment;
            }

            $url .= $fragment;
        }

        return $url;
    }
}
