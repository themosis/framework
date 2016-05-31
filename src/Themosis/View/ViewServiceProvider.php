<?php

namespace Themosis\View;

use Themosis\Foundation\ServiceProvider;
use Themosis\View\Compilers\ScoutCompiler;
use Themosis\View\Engines\EngineResolver;
use Themosis\View\Engines\PhpEngine;
use Themosis\View\Engines\ScoutEngine;
use Themosis\View\Engines\TwigEngine;
use Themosis\View\Extensions\ThemosisTwigExtension;

class ViewServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerViewFinder();
        $this->registerTwigEnvironment();
        $this->registerEngineResolver();
        $this->registerViewFactory();
        $this->registerLoop();
    }

    /**
     * Register the EngineResolver instance to the application.
     */
    protected function registerEngineResolver()
    {
        $serviceProvider = $this;

        $this->app->singleton('view.engine.resolver', function () use ($serviceProvider) {
            $resolver = new EngineResolver();

            // Register the engines.
            foreach (['php', 'scout', 'twig'] as $engine) {
                $serviceProvider->{'register'.ucfirst($engine).'Engine'}($engine, $resolver);
            }

            return $resolver;
        });
    }

    /**
     * Register the PHP engine to the EngineResolver.
     *
     * @param string                                $engine   Name of the engine.
     * @param \Themosis\View\Engines\EngineResolver $resolver
     */
    protected function registerPhpEngine($engine, EngineResolver $resolver)
    {
        $resolver->register($engine, function () {
            return new PhpEngine();
        });
    }

    /**
     * Register the Scout engine to the EngineResolver.
     *
     * @param string                                $engine   Name of the engine.
     * @param \Themosis\View\Engines\EngineResolver $resolver
     */
    protected function registerScoutEngine($engine, EngineResolver $resolver)
    {
        $container = $this->app;

        // Register a ScoutCompiler instance so we can
        // inject it into the ScoutEngine class.
        $storage = $container['path.storage'].'views'.DS;
        $container->singleton('scout.compiler', new ScoutCompiler($storage));

        $resolver->register($engine, function () use ($container) {
            return new ScoutEngine($container['scout.compiler']);
        });
    }

    /**
     * Register the Twig engine to the EngineResolver.
     * 
     * @param string         $engine
     * @param EngineResolver $resolver
     */
    protected function registerTwigEngine($engine, EngineResolver $resolver)
    {
        $container = $this->app;

        $resolver->register($engine, function () use ($container) {

            // Set the loader main namespace (paths).
            $container['twig.loader']->setPaths($container['view.finder']->getPaths());

            return new TwigEngine($container['twig'], $container['view.finder']);
        });
    }

    /**
     * Register Twig environment and its loader.
     */
    protected function registerTwigEnvironment()
    {
        $container = $this->app;

        // Twig Filesystem loader.
        $container->singleton('twig.loader', 'Twig_Loader_Filesystem');

        // Twig
        $container->singleton('twig', function($container)
        {
            return new \Twig_Environment($container['twig.loader'], [
                'auto_reload' => true,
                'cache' => $container['path.storage'].'twig',
            ]);
        });

        // Add the dump Twig extension.
        $container['twig']->addExtension(new \Twig_Extension_Debug());

        // Check if debug constant exists and set to true.
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $container['twig']->enableDebug();
        }

        // Provides WordPress functions and more to Twig templates.
        $container['twig']->addExtension(new ThemosisTwigExtension($container));
    }

    /**
     * Register the ViewFinder instance.
     */
    protected function registerViewFinder()
    {
        $this->app->instance('view.finder', new ViewFinder());
    }

    /**
     * Register the view factory. The factory is
     * available in all views.
     */
    protected function registerViewFactory()
    {
        $container = $this->app;

        $factory = new ViewFactory($container['view.engine.resolver'], $container['view.finder'], $container['action']);
        $factory->setContainer($container);
        $factory->share('__app', $container);

        $container->instance('view', $factory);
    }

    /**
     * Register the loop helper class.
     */
    protected function registerLoop()
    {
        $this->app->instance('loop', new Loop());
    }
}
