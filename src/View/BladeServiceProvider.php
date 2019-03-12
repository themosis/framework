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
        Blade::directive('can', function ($expression) {
            return "<?php if( User::current()->can({$expression}) ): ?>";
        });

        Blade::directive('endcan', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('endloggedin', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('endloggedout', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('endloop', function () {
            return '<?php }} ?>';
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
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

        Blade::directive('loggedin', function () {
            return '<?php if( is_user_logged_in() ): ?>';
        });

        Blade::directive('loggedout', function () {
            return '<?php if( !is_user_logged_in() ): ?>';
        });

        Blade::directive('loop', function () {
            return '<?php if (have_posts()) { while (have_posts()) { the_post(); ?>';
        });

        Blade::directive('role', function ($expression) {
            return "<?php if( User::current()->hasRole({$expression}) ): ?>";
        });

        Blade::directive('query', function ($expression) {
            return '<?php $_query = (is_array('.$expression.')) ? new \WP_Query('.$expression.') : '.$expression.'; if ($_query->have_posts()) { while ($_query->have_posts()) { $_query->the_post(); ?>';
        });

        /**
         * Simulate a WordPress get_template_part() behavior using custom views.
         *
         * Examples:
         * "@template('parts.content', get_post_type())"
         * "@template('parts.content', 'page')"
         *
         * In the first example, the view factory will try to include a "dynamic"
         * view with the following path "parts.content-post" or "parts.content-attachment".
         *
         * In the second example, the view factory tries to include the
         * "parts.content-page" view.
         *
         * We test if the dynamic view exists before trying to render it. If none is found,
         * we render the view defined by the first argument. In the 2 examples, the view
         * "parts.content" is rendered and should therefor exists.
         *
         * As a third argument, you can pass custom data array to the included view.
         */
        Blade::directive('template', function ($expression) {
            // Get a list of passed arguments.
            $args = array_map(function ($arg) {
                return trim($arg, '\/\'\" ()');
            }, explode(',', $expression));

            // Set the view path.
            if (isset($args[1])) {
                if (is_callable($args[1])) {
                    $args[1] = call_user_func($args[1]);
                }

                $path = $args[0].'-'.$args[1];
            } else {
                $path = $args[0];
            }

            // Set the view data if defined.
            $data = 3 === count($args) ? array_pop($args) : '[]';

            return "<?php if (\$__env->exists('{$path}')) { echo \$__env->make('{$path}', {$data}, array_except(get_defined_vars(), array('__data', '__path')))->render(); } else { echo \$__env->make('{$args[0]}', {$data}, array_except(get_defined_vars(), array('__data', '__path')))->render(); } ?>";
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
