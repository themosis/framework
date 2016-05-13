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
    protected $provides = [
        'view.engine.resolver',
        'php',
        'scout',
        'scout.compiler',
        'view.finder',
        'view',
        'loop',
        'twig',
        'twig.loader',
    ];

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

        $this->getContainer()->add('view.engine.resolver', function () use ($serviceProvider) {
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
     * Register the Twig engine to the EngineResolver.
     * 
     * @param string         $engine
     * @param EngineResolver $resolver
     */
    protected function registerTwigEngine($engine, EngineResolver $resolver)
    {
        $container = $this->getContainer();

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
        $container = $this->getContainer();

        // Twig Filesystem loader.
        $container->share('twig.loader', 'Twig_Loader_Filesystem');

        // Twig
        $container->share('twig', 'Twig_Environment')->withArgument('twig.loader')->withArgument([
            'auto_reload' => true,
            'cache' => $container['path.storage'].'twig',
        ]);

        // Add the dump Twig extension.
        $container['twig']->addExtension(new \Twig_Extension_Debug());

        // Check if debug constant exists and set to true.
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $container['twig']->enableDebug();
        }

        // Provides a global 'wp' object in order to call WordPress and core PHP functions.
        $container['twig']->addExtension(new ThemosisTwigExtension());
    }

    /**
     * Register the ViewFinder instance.
     */
    protected function registerViewFinder()
    {
        $this->getContainer()->add('view.finder', new ViewFinder());
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
