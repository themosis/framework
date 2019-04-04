<?php

namespace Themosis\View\Extensions;

class WordPress extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * Register Twig templates globals.
     * The "fn" global can be used to call any
     * WordPress or core PHP functions.
     *
     * @return array
     */
    public function getGlobals()
    {
        return [
            'fn' => $this
        ];
    }

    /**
     * Allow developers to call WordPress and core PHP
     * functions inside their Twig templates.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        return call_user_func_array($name, $arguments);
    }

    /**
     * Register a list of WordPress functions for use
     * inside Twig templates.
     *
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            /**
             * Theme functions
             */
            new \Twig_Function('body_class', function ($class = '') {
                return body_class($class);
            }),
            new \Twig_Function('post_class', function ($class = '', $post_id = null) {
                return post_class($class, $post_id);
            }),
            new \Twig_Function('wp_head', 'wp_head'),
            new \Twig_Function('wp_footer', 'wp_footer'),

            /**
             * Helper functions
             */
            new \Twig_Function('fn', function ($name) {
                $args = func_get_args();

                // By default, the function signature should be the first argument.
                // Let's remove it from the list of arguments.
                array_shift($args);

                return call_user_func_array(trim($name), $args);
            }),
            new \Twig_Function('meta', function ($key, $id = null, $context = 'post', $single = true) {
                return meta($key, $id, $context, $single);
            }),

            /**
             * Translations functions
             */
            new \Twig_Function('__', function ($text, $domain = 'default') {
                return __($text, $domain);
            }),
            new \Twig_Function('_e', function ($text, $domain = 'default') {
                return _e($text, $domain);
            }),
            new \Twig_Function('_ex', function ($text, $context, $domain = 'default') {
                return _ex($text, $context, $domain);
            }),
            new \Twig_Function('_n', function ($singular, $plural, $number, $domain = 'default') {
                return _n($singular, $plural, $number, $domain);
            }),
            new \Twig_Function('_n_noop', function ($singular, $plural, $domain = 'default') {
                return _n_noop($singular, $plural, $domain);
            }),
            new \Twig_Function('_nx', function ($singular, $plural, $number, $context, $domain = 'default') {
                return _nx($singular, $plural, $number, $context, $domain);
            }),
            new \Twig_Function('_nx_noop', function ($singular, $plural, $context, $domain = 'default') {
                return _nx_noop($singular, $plural, $context, $domain);
            }),
            new \Twig_Function('_x', function ($text, $context, $domain = 'default') {
                return _x($text, $context, $domain);
            }),
            new \Twig_Function('translate', function ($text, $domain = 'default') {
                return translate($text, $domain);
            }),
            new \Twig_Function('translate_nooped_plural', function ($plural, $count, $domain = 'default') {
                return translate_nooped_plural($plural, $count, $domain);
            })
        ];
    }

    /**
     * Register a list of WordPress filters for use
     * inside Twig templates.
     *
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            /**
             * Formatting filters.
             */
            new \Twig_Filter('wpantispam', function ($email, $encoding = 0) {
                return antispambot($email, $encoding);
            }),
            new \Twig_Filter('wpautop', function ($text, $br = true) {
                return wpautop($text, $br);
            }),
            new \Twig_Filter('wpnofollow', function ($text) {
                return wp_rel_nofollow($text);
            }),
            new \Twig_Filter('wptrimexcerpt', function ($text) {
                return wp_trim_excerpt($text);
            }),
            new \Twig_Filter('wptrimwords', function ($text, $num_words = 55, $more = null) {
                return wp_trim_words($text, $num_words, $more);
            }),
            new \Twig_Filter('zeroise', function ($number, $treshold = 4) {
                return zeroise($number, $treshold);
            })
        ];
    }
}
