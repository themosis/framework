<?php

namespace Themosis\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Themosis\Support\Facades\User;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::if('wp_can', function ($capability) {
            return User::current()->can(...func_get_args());
        });

        Blade::directive('wp_footer', function () {
            return '<?php wp_footer(); ?>';
        });

        Blade::directive('wp_head', function () {
            return '<?php wp_head(); ?>';
        });

        Blade::if('wp_logged_in', function () {
            return is_user_logged_in();
        });

        Blade::if('wp_role', function (string $role) {
            return User::current()->hasRole($role);
        });

        Blade::directive('loop', function () {
            return '<?php if (have_posts()) { $__in_loop = true; while (have_posts()) { the_post(); ?>';
        });

        Blade::directive('loopelse', function () {
            return '<?php }} else { ?>';
        });

        Blade::directive('endloop', function () {
            return '<?php isset($__in_loop) && $__in_loop ? }} unset($__in_loop); : } ?>';
        });

        Blade::directive('query', function ($expression) {
            return '<?php $_query = (is_array('.$expression.')) ? new \WP_Query('.$expression.') : '.$expression.'; if ($_query->have_posts()) { $__in_loop = true; while ($_query->have_posts()) { $_query->the_post(); ?>';
        });

        Blade::directive('queryelse', function () {
            return '<?php }} else { ?>';
        });

        Blade::directive('endquery', function () {
            return '<?php isset($__in_loop) && $__in_loop ? }} wp_reset_postdata(); unset($__in_loop); : } ?>';
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

            return "<?php if (\$__env->exists('{$path}')) { echo \$__env->make('{$path}', {$data}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); } else { echo \$__env->make('{$args[0]}', {$data}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); } ?>";
        });
    }
}
