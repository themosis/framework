<?php

namespace Themosis\View\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class WordPress extends AbstractExtension implements GlobalsInterface
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
            new TwigFunction('body_class', function ($class = '') {
                return body_class($class);
            }),
            new TwigFunction('post_class', function ($class = '', $post_id = null) {
                return post_class($class, $post_id);
            }),
            new TwigFunction('wp_head', 'wp_head'),
            new TwigFunction('wp_footer', 'wp_footer'),

            /**
             * Helper functions
             */
            new TwigFunction('fn', function ($name) {
                $args = func_get_args();

                // By default, the function signature should be the first argument.
                // Let's remove it from the list of arguments.
                array_shift($args);

                return call_user_func_array(trim($name), $args);
            }),
            new TwigFunction('meta', function ($object_id, $meta_key = '', $single = false, $meta_type = 'post') {
                return meta($object_id, $meta_key, $single, $meta_type);
            }),

            /**
             * Translations functions
             */
            new TwigFunction('__', function ($text, $domain = 'default') {
                return __($text, $domain);
            }),
            new TwigFunction('_e', function ($text, $domain = 'default') {
                return _e($text, $domain);
            }),
            new TwigFunction('_ex', function ($text, $context, $domain = 'default') {
                return _ex($text, $context, $domain);
            }),
            new TwigFunction('_n', function ($singular, $plural, $number, $domain = 'default') {
                return _n($singular, $plural, $number, $domain);
            }),
            new TwigFunction('_n_noop', function ($singular, $plural, $domain = 'default') {
                return _n_noop($singular, $plural, $domain);
            }),
            new TwigFunction('_nx', function ($singular, $plural, $number, $context, $domain = 'default') {
                return _nx($singular, $plural, $number, $context, $domain);
            }),
            new TwigFunction('_nx_noop', function ($singular, $plural, $context, $domain = 'default') {
                return _nx_noop($singular, $plural, $context, $domain);
            }),
            new TwigFunction('_x', function ($text, $context, $domain = 'default') {
                return _x($text, $context, $domain);
            }),
            new TwigFunction('translate', function ($text, $domain = 'default') {
                return translate($text, $domain);
            }),
            new TwigFunction('translate_nooped_plural', function ($plural, $count, $domain = 'default') {
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
            new TwigFilter('wpantispam', function ($email, $encoding = 0) {
                return antispambot($email, $encoding);
            }),
            new TwigFilter('wpautop', function ($text, $br = true) {
                return wpautop($text, $br);
            }),
            new TwigFilter('wpnofollow', function ($text) {
                return wp_rel_nofollow($text);
            }),
            new TwigFilter('wptrimexcerpt', function ($text) {
                return wp_trim_excerpt($text);
            }),
            new TwigFilter('wptrimwords', function ($text, $num_words = 55, $more = null) {
                return wp_trim_words($text, $num_words, $more);
            }),
            new TwigFilter('zeroise', function ($number, $treshold = 4) {
                return zeroise($number, $treshold);
            })
        ];
    }
}
