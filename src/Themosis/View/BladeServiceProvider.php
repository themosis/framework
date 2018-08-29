<?php

namespace Themosis\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register bindings.
     */
    public function register()
    {
        $this->app->bind('loop', function () {
            return new Loop();
        });
    }

    /**
     * Register Blade custom directives.
     */
    public function boot()
    {
        Blade::directive('endloop', function () {
            return '<?php }} ?>';
        });

        Blade::directive('endquery', function () {
            return '<?php }} wp_reset_postdata(); ?>';
        });

        Blade::directive('footer', function () {
            return '<?php wp_footer(); ?>';
        });

        Blade::directive('head', function () {
            return '<?php wp_head(); ?>';
        });

        Blade::directive('loop', function () {
            return '<?php if (have_posts()) { while (have_posts()) { the_post(); ?>';
        });

        Blade::directive('query', function ($expression) {
            return '<?php $_query = (is_array('.$expression.')) ? new \WP_Query('.$expression.') : '.$expression.'; if ($_query->have_posts()) { while ($_query->have_posts()) { $_query->the_post(); ?>';
        });

        Blade::directive('wp_footer', function () {
            return '<?php wp_footer(); ?>';
        });

        Blade::directive('wp_head', function () {
            return '<?php wp_head(); ?>';
        });

        Blade::directive('wpfooter', function () {
            return '<?php wp_footer(); ?>';
        });

        Blade::directive('wphead', function () {
            return '<?php wp_head(); ?>';
        });
    }
}
