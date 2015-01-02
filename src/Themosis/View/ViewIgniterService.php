<?php
namespace Themosis\View;

use Themosis\Core\IgniterService;
use Themosis\View\Compilers\ScoutCompiler;
use Themosis\View\Engines\EngineResolver;
use Themosis\View\Engines\PhpEngine;
use Themosis\View\Engines\ScoutEngine;

class ViewIgniterService extends IgniterService{

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->igniteEngineResolver();
        $this->igniteViewFinder();
        $this->igniteViewFactory();
        $this->igniteLoop();
    }

    /**
     * Register the EngineResolver instance to the application.
     *
     * @return void
     */
    private function igniteEngineResolver()
    {
        $igniterService = $this;

        $this->app->bindShared('view.engine.resolver', function() use ($igniterService){

            $resolver = new EngineResolver();

            // Register the engines.
            foreach (array('php', 'scout') as $engine)
            {
                $igniterService->{'register'.ucfirst($engine).'Engine'}($engine, $resolver);
            }

            return $resolver;

        });
    }

    /**
     * Register the PHP engine to the EngineResolver.
     *
     * @param string $engine Name of the engine.
     * @param \Themosis\View\Engines\EngineResolver $resolver
     * @return void
     */
    private function registerPhpEngine($engine, EngineResolver $resolver)
    {
        $resolver->register($engine, function(){
            return new PhpEngine();
        });
    }

    /**
     * Register the Scout engine to the EngineResolver.
     *
     * @param string $engine Name of the engine.
     * @param \Themosis\View\Engines\EngineResolver $resolver
     * @return void
     */
    private function registerScoutEngine($engine, EngineResolver $resolver)
    {
        $app = $this->app;

        // Register a ScoutCompiler instance so we can
        // inject it into the ScoutEngine class.
        $app->bindShared('scout.compiler', function($app){

            $storage = $app['path.storage'].'views'.DS;

            return new ScoutCompiler($storage);

        });

        $resolver->register($engine, function() use ($app){
            return new ScoutEngine($app['scout.compiler']);
        });
    }

    /**
     * Register the ViewFinder instance.
     *
     * @return void
     */
    private function igniteViewFinder()
    {
        $this->app->bindShared('view.finder', function($app){

            // Paths to view directories.
            $paths = apply_filters('themosisViewPaths', array());

            return new ViewFinder($paths);

        });
    }

    /**
     * Register the view factory. The factory is
     * available in all views.
     *
     * @return void
     */
    private function igniteViewFactory()
    {
        $this->app->bindShared('view', function($app){

            $viewEnv = new ViewFactory($app['view.engine.resolver'], $app['view.finder']);

            // Set the IoC container.
            $viewEnv->setContainer($app);

            // Register the container as a shared view data.
            $viewEnv->share('__app', $app);

            return $viewEnv;

        });
    }

    /**
     * Register the loop helper class.
     *
     * @return void
     */
    private function igniteLoop()
    {
        $this->app->bindShared('loop', function($app){

            return new Loop();

        });
    }
}