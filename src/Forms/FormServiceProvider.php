<?php

namespace Themosis\Forms;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use Themosis\Forms\Resources\Factory;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Register our form service.
     */
    public function register()
    {
        $this->registerFractalManager();

        /** @var \Illuminate\View\Factory $view */
        $view = $this->app['view'];
        $view->addLocation(__DIR__.'/views');

        $this->app->singleton('form', function ($app) {
            return new FormFactory(
                $app['validator'],
                $app['view'],
                $app['league.fractal'],
                new Factory()
            );
        });
    }

    /**
     * Register the PHP League Fractal manager class.
     */
    protected function registerFractalManager()
    {
        $this->app->bind('league.fractal', function () {
            return new Manager();
        });
    }

    /**
     * Register form Blade directives.
     */
    public function boot()
    {
        // Render a form.
        // The directive needs the form instance as a
        // first parameter.
        Blade::directive('form', function ($expression) {
            return "<?php echo ($expression)->render(); ?>";
        });

        // Render a form open tag.
        // The directive needs the form instance as a
        // first parameter.
        Blade::directive('formOpen', function ($expression) {
            return "<?php echo ($expression)->open(); ?>";
        });

        // Render a form close tag.
        // The directive needs the form instance as a
        // first parameter.
        Blade::directive('formClose', function ($expression) {
            return "<?php echo ($expression)->close(); ?>";
        });

        // Render a form field.
        // First parameter is the form instance and the
        // second parameter is a string representing the field name
        // without a prefix - as registered when building the form.
        Blade::directive('formField', function ($expression) {
            list($form, $fieldName) = array_map('trim', explode(',', $expression));

            return "<?php echo ($form)->repository()->getFieldByName($fieldName)->render(); ?>";
        });
    }
}
