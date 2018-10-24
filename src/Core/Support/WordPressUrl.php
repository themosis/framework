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
}
