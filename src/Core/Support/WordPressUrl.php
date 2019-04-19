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
     * Format the home URL. Make sure that it does not contain the "/cms" fragment.
     *
     * @param string $url
     * @param string $fragment
     *
     * @return string
     */
    public function formatHomeUrl(string $url, string $fragment = 'cms')
    {
        $length = (int) strlen($fragment) * -1;

        if (substr($url, $length) === $fragment) {
            $url = substr($url, 0, $length);
            $url = rtrim($url, '/');
        }

        return $url;
    }

    /**
     * Format the site URL. If the URL does not contain the fragment,
     * append it with a forward slash (if not inside the fragment) on the URL,
     * but only if the current site is the main site or a subdomain site.
     *
     * @param string $url
     * @param string $fragment
     *
     * @return string
     */
    public function formatSiteUrl(string $url, string $fragment = 'cms')
    {
        $length = (int) strlen($fragment) * -1;

        if (substr($url, $length) !== $fragment && (is_main_site() || is_subdomain_install())) {
            if (strpos($fragment, '/') === false) {
                $fragment = '/'.$fragment;
            }

            $url .= $fragment;
        }

        return $url;
    }

    /**
     * Format the network URL. If the URL is missing the WordPress directory
     * fragment, it adds it before the common delimiter.
     *
     * @param string $url
     * @param string $delimiter
     * @param string $fragment
     *
     * @return string
     */
    public function formatNetworkUrl(string $url, string $delimiter = 'wp-admin', string $fragment = 'cms')
    {
        return $this->formatUrl($url, $delimiter, $fragment);
    }
}
