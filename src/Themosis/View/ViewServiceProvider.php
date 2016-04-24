<?php

namespace Themosis\View;

use Themosis\Foundation\ServiceProvider;
use Themosis\View\Compilers\ScoutCompiler;
use Themosis\View\Engines\EngineResolver;
use Themosis\View\Engines\PhpEngine;
use Themosis\View\Engines\ScoutEngine;

class ViewServiceProvider extends ServiceProvider
{
    protected $provides = [
        'view.engine.resolver',
        'php',
        'scout',
        'scout.compiler',
        'view.finder',
        'view',
        'loop',
    ];

    public function register()
    {
        $this->registerEngineResolver();
        $this->registerViewFinder();
        $this->registerViewFactory();
        $this->registerLoop();
    }

    /**
     * Register the EngineResolver instance to the application.
     */
    protected function registerEngineResolver()
    {
        $serviceProvider = $this;

        $this->getContainer()->add('view.engine.resolver', function () use ($serviceProvider) {
            $resolver = new EngineResolver();

            // Register the engines.
            foreach (['php', 'scout'] as $engine) {
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
        $container = $this->getContainer();

        // Register a ScoutCompiler instance so we can
        // inject it into the ScoutEngine class.
        $storage = $container->get('path.storage').'views'.DS;
        $container->add('scout.compiler', new ScoutCompiler($storage));

        $resolver->register($engine, function () use ($container) {
            return new ScoutEngine($container->get('scout.compiler'));
        });
    }

    /**
     * Register the ViewFinder instance.
     */
    protected function registerViewFinder()
    {
        // Paths to view directories.
        $paths = apply_filters('themosis_views', []);

        $this->getContainer()->add('view.finder', new ViewFinder($paths));
    }

    /**
     * Register the view factory. The factory is
     * available in all views.
     */
    protected function registerViewFactory()
    {
        $container = $this->getContainer();

        $factory = new ViewFactory($container->get('view.engine.resolver'), $container->get('view.finder'), $container->get('action'));
        $factory->setContainer($container);
        $factory->share('__app', $container);

        $container->add('view', $factory);
    }

    /**
     * Register the loop helper class.
     */
    protected function registerLoop()
    {
        $this->getContainer()->add('loop', new Loop());
    }
}
