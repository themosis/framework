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
        Blade::directive('form', function ($expression) {
            return "<?php echo ($expression)->render(); ?>";
        });

        Blade::directive('formOpen', function ($expression) {
            return "<?php echo ($expression)->open(); ?>";
        });

        Blade::directive('formClose', function ($expression) {
            return "<?php echo ($expression)->close(); ?>";
        });
    }
}
