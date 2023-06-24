<?php

namespace Themosis\Tests;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Themosis\View\FileViewFinder;

trait ViewFactory
{
    /**
     * @var Factory
     */
    protected $viewFactory;

    /**
     * Return a View factory instance.
     *
     * @param  \Themosis\Core\Application  $app
     * @param  array  $paths
     * @return Factory
     */
    public function getViewFactory($app, $paths = [])
    {
        if (! is_null($this->viewFactory)) {
            return $this->viewFactory;
        }

        $filesystem = new Filesystem();

        $bladeCompiler = new BladeCompiler(
            $filesystem,
            __DIR__.'/storage/views',
        );

        $app->instance('blade', $bladeCompiler);

        $resolver = new EngineResolver();

        $resolver->register('php', function () {
            return new PhpEngine();
        });

        $resolver->register('blade', function () use ($bladeCompiler) {
            return new CompilerEngine($bladeCompiler);
        });

        $factory = new Factory(
            $resolver,
            $viewFinder = new FileViewFinder(
                $filesystem,
                $paths,
                ['blade.php', 'php'],
            ),
            new Dispatcher($app),
        );

        $factory->addExtension('blade', $resolver);
        $factory->setContainer($app);

        $this->viewFactory = $factory;

        return $factory;
    }
}
