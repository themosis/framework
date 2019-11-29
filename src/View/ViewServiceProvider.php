<?php

namespace Themosis\View;

use Illuminate\View\Factory;
use Illuminate\View\ViewServiceProvider as IlluminateViewServiceProvider;
use Themosis\View\Engines\Twig;
use Themosis\View\Extensions\WordPress;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class ViewServiceProvider extends IlluminateViewServiceProvider
{
    /**
     * Register view bindings.
     */
    public function register()
    {
        parent::register();
        $this->registerTwigLoader();
        $this->registerTwigEnvironment();
        $this->registerTwigEngine();
    }

    /**
     * Register Themosis view finder.
     */
    public function registerViewFinder()
    {
        $this->app->singleton('view.finder', function ($app) {
            return new FileViewFinder($app['files'], $app['config']['view.paths']);
        });
    }

    /**
     * Register Twig Loader.
     */
    public function registerTwigLoader()
    {
        $this->app->singleton('twig.loader', function ($app) {
            return new FilesystemLoader($app['view.finder']->getPaths());
        });
    }

    /**
     * Register Twig Environment.
     */
    public function registerTwigEnvironment()
    {
        $this->app->singleton('twig', function ($app) {
            $twig = new Environment(
                $app['twig.loader'],
                [
                    'auto_reload' => true,
                    'cache' => $app['config']['view.twig']
                ]
            );

            // Add Twig Debug Extension
            $twig->addExtension(new DebugExtension());

            // Enable debug.
            if ($app['config']['app.debug']) {
                $twig->enableDebug();
            }

            // Add WordPress helpers extension.
            $twig->addExtension(new WordPress());

            return $twig;
        });
    }

    /**
     * Register the Twig engine implementation.
     */
    public function registerTwigEngine()
    {
        /** @var Factory $factory */
        $factory = $this->app['view'];
        $factory->addExtension('twig', 'twig', function () {
            return new Twig($this->app['twig'], $this->app['view.finder']);
        });
    }
}
