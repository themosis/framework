<?php

namespace Themosis\View\Extensions;

use Themosis\Foundation\Application;
use Twig_SimpleFunction;
use Twig_Extension;

class ThemosisTwigExtension extends Twig_Extension
{
    /**
     * @var \Themosis\Foundation\Application
     */
    protected $container;

    public function __construct(Application $container)
    {
        $this->container = $container;
    }

    /**
     * Define the extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'themosis';
    }

    /**
     * Register a global "fn" which can be used
     * to call any WordPress or core PHP functions.
     *
     * @return array
     */
    public function getGlobals()
    {
        return [
            'fn' => $this,
            'Form' => $this->container['form'],
        ];
    }

    /**
     * Allow developers to call core php and WordPress functions
     * using the `fn` namespace inside their templates.
     * Linked to the global call only...
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
     * Register a list of functions available into Twig templates.
     * 
     * @return array|\Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return [
            /*
             * WordPress theme functions.
             */
            new Twig_SimpleFunction('wp_head', 'wp_head'),
            new Twig_SimpleFunction('wp_footer', 'wp_footer'),
            new Twig_SimpleFunction('body_class', function ($class = '') {
                return body_class($class);
            }),
            new Twig_SimpleFunction('post_class', function ($class = '', $id = null) {
                return post_class($class, $id);
            }),
            /*
             * WordPress formatting functions.
             */
            new Twig_SimpleFunction('wpautop', function ($text, $br = true) {
                return wpautop($text, $br);
            }),
            new Twig_SimpleFunction('wp_trim_words', function ($text, $num_words = 55, $more = null) {
                return wp_trim_words($text, $num_words, $more);
            }),
            /*
             * Use this to call any core, WordPress or user defined functions.
             */
            new Twig_SimpleFunction('fn', function ($functionName) {
                $args = func_get_args();
                // By default, the function name should always be the first argument.
                // This remove it from the arguments list.
                array_shift($args);

                if (is_string($functionName)) {
                    $functionName = trim($functionName);
                }

                return call_user_func_array($functionName, $args);
            }),
            /*
             * Retrieve any meta data from post, comment, user, ...
             */
            new Twig_SimpleFunction('meta', function ($key, $id = null, $context = 'post', $single = true) {
                return meta($key, $id, $context, $single);
            }),
            /*
             * Gettext functions.
             */
            new Twig_SimpleFunction('translate', function ($text, $domain = 'default') {
                return translate($text, $domain);
            }),
            new Twig_SimpleFunction('__', function ($text, $domain = 'default') {
                return __($text, $domain);
            }),
            new Twig_SimpleFunction('_e', function ($text, $domain = 'default') {
                return _e($text, $domain);
            }),
            new Twig_SimpleFunction('_n', function ($single, $plural, $number, $domain = 'default') {
                return _n($single, $plural, $number, $domain);
            }),
            new Twig_SimpleFunction('_x', function ($text, $context, $domain = 'default') {
                return _x($text, $context, $domain);
            }),
            new Twig_SimpleFunction('_ex', function ($text, $context, $domain = 'default') {
                return _ex($text, $context, $domain);
            }),
            new Twig_SimpleFunction('_nx', function ($single, $plural, $number, $context, $domain = 'default') {
                return _nx($single, $plural, $number, $context, $domain);
            }),
            new Twig_SimpleFunction('_n_noop', function ($singular, $plural, $domain = 'default') {
                return _n_noop($singular, $plural, $domain);
            }),
            new Twig_SimpleFunction('_nx_noop', function ($singular, $plural, $context, $domain = 'default') {
                return _nx_noop($singular, $plural, $context, $domain);
            }),
            new Twig_SimpleFunction('translate_nooped_plural', function ($nooped_plural, $count, $domain = 'default') {
                return translate_nooped_plural($nooped_plural, $count, $domain);
            }),
        ];
    }
}
