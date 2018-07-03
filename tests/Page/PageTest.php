<?php

namespace Themosis\Tests\Page;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\FileViewFinder;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Page\PageFactory;

class PageTest extends TestCase
{
    protected $viewFactory;

    public function getActionMock()
    {
        return $this->getMockBuilder(\Themosis\Hook\ActionBuilder::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getViewFactory()
    {
        if (! is_null($this->viewFactory)) {
            return $this->viewFactory;
        }

        $application = new Application();

        $filesystem = new Filesystem();

        $bladeCompiler = new BladeCompiler(
            $filesystem,
            __DIR__.'/../storage/views'
        );
        $application->instance('blade', $bladeCompiler);

        $resolver = new EngineResolver();

        $resolver->register('php', function () {
            return new PhpEngine();
        });

        $resolver->register('blade', function () use ($bladeCompiler) {
            return new CompilerEngine($bladeCompiler);
        });

        $factory = new \Illuminate\View\Factory(
            $resolver,
            $viewFinder = new FileViewFinder($filesystem, [
                __DIR__.'/../../../framework/src/Themosis/Page/views/',
                __DIR__.'/views/'
            ], ['blade.php', 'php']),
            new Dispatcher($application)
        );

        $factory->addExtension('blade', $resolver);
        $factory->setContainer($application);

        $this->viewFactory = $factory;

        return $factory;
    }

    public function getFactory($action)
    {
        return new PageFactory($action, $this->getViewFactory());
    }

    public function testCreateACustomPage()
    {
        $action = $this->getActionMock();
        $factory = $this->getFactory($action);

        $page = $factory->make('a-page', 'Custom Page');

        $this->assertInstanceOf(\Themosis\Page\Page::class, $page);
        $this->assertEquals('a-page', $page->getSlug());
        $this->assertEquals('Custom Page', $page->getTitle());
        $this->assertEquals('Custom Page', $page->getMenu());
        $this->assertEquals('manage_options', $page->getCapability());
        $this->assertEquals('dashicons-admin-generic', $page->getIcon());
        $this->assertEquals(21, $page->getPosition());
        $this->assertNull($page->getParent());
        $this->assertFalse($page->isNetwork());
        $this->assertInstanceOf(\Themosis\Page\PageView::class, $page->ui());
        $this->assertEquals('themosis', $page->ui()->getTheme());
        $this->assertEquals('default', $page->ui()->getLayout());
        $this->assertEquals('page', $page->ui()->getViewPath());

        $action->expects($this->once())->method('add');

        $page->set();
    }

    public function testCreateANetworkPage()
    {
        $action = $this->getActionMock();
        $factory = $this->getFactory($action);

        $page = $factory->make('settings', 'Network Options')
            ->network();

        $this->assertTrue($page->isNetwork());

        $action->expects($this->once())->method('add');

        $page->set();
    }

    public function testCreatePageWithCustomView()
    {
        $factory = $this->getFactory($this->getActionMock());

        $page = $factory->make('custom', 'A Page');
        $page->ui()->setView('custom');
        $page->with('name', 'Marcel');
        $page->with('__page', $page);
        $page->with([
            'some' => 'value',
            'key' => 42
        ]);
        $page->setCapability('custom_cap');
        $page->set();

        $this->assertEquals('custom', $page->ui()->getViewPath());
        $this->assertEquals('themosis.default.custom', $page->ui()->getView()->name());
        $this->assertEquals('custom_cap', $page->getCapability());

        $view = $page->ui()->getView();
        $this->assertEquals('Marcel', $view['name']);
        $this->assertInstanceOf(\Themosis\Page\Page::class, $view['__page']);
        $this->assertEquals('value', $view['some']);
        $this->assertEquals(42, $view['key']);
    }
}
