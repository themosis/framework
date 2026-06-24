<?php

namespace Themosis\Tests\View;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\View\FileViewFinder;
use Themosis\View\ViewServiceProvider;

class ViewServiceProviderTest extends TestCase
{
    private function makeApplication(): Application
    {
        $basePath = realpath(__DIR__ . '/../');
        $app = new Application($basePath);
        $app->instance('files', new Filesystem());

        return $app;
    }

    public function testViewServiceProviderBindsViewFinderSingleton()
    {
        $app = $this->makeApplication();
        $app->instance('config', new ConfigRepository([
            'view' => ['paths' => [__DIR__]],
        ]));

        $provider = new ViewServiceProvider($app);
        $provider->registerViewFinder();

        $this->assertInstanceOf(FileViewFinder::class, $app->make('view.finder'));
    }

    public function testViewFinderResolvedFromContainerIsSingleton()
    {
        $app = $this->makeApplication();
        $app->instance('config', new ConfigRepository([
            'view' => ['paths' => [__DIR__]],
        ]));

        $provider = new ViewServiceProvider($app);
        $provider->registerViewFinder();

        $finderA = $app->make('view.finder');
        $finderB = $app->make('view.finder');

        $this->assertSame($finderA, $finderB);
    }
}
