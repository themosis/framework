<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\ProviderRepository;

class TestDeferredProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        // Do nothing
    }

    public function isDeferred()
    {
        return true;
    }

    public function provides()
    {
        return ['foo.provides1', 'foo.provides2'];
    }

    public function when()
    {
        return [];
    }
}

class ProviderRepositoryTest extends TestCase
{
    public function testServicesAreRegisteredWhenManifestIsNotRecompiled()
    {
        $app = $this->getMockBuilder('Themosis\Core\Application')
            ->onlyMethods(['register', 'addDeferredServices', 'runningInConsole'])
            ->getMock();
        $repository = $this->getMockBuilder('Themosis\Core\ProviderRepository')
            ->setConstructorArgs([
                $app,
                $this->getMockBuilder('Illuminate\Filesystem\Filesystem')->getMock(),
                __DIR__ . '/services.php',
            ])
            ->onlyMethods([
                'loadManifest',
                'shouldRecompile',
                'compileManifest',
                'createProvider',
            ])
            ->getMock();
        $repository->expects($this->once())
            ->method('loadManifest')
            ->willReturn([
                'eager' => ['foo'],
                'deferred' => ['deferred'],
                'providers' => ['providers'],
                'when' => [],
            ]);
        $repository->expects($this->once())
            ->method('shouldRecompile')
            ->willReturn(false);
        $app->expects($this->once())->method('register')->with('foo');
        $app->expects($this->any())->method('runningInConsole')->willReturn(false);
        $app->expects($this->once())->method('addDeferredServices')->with(['deferred']);

        $repository->load([]);
    }

    public function testManifestIsProperlyRecompiled()
    {
        $app = $this->getMockBuilder('Themosis\Core\Application')
            ->onlyMethods(['register', 'addDeferredServices', 'runningInConsole'])
            ->getMock();
        $app->method('register');
        $app->method('runningInConsole')->willReturn(false);

        $repository = $this->getMockBuilder('Themosis\Core\ProviderRepository')
            ->setConstructorArgs([
                $app,
                $this->getMockBuilder('Illuminate\Filesystem\Filesystem')->getMock(),
                __DIR__ . '/services.php',
            ])
            ->onlyMethods(['loadManifest', 'shouldRecompile', 'createProvider'])
            ->getMock();

        $repository->method('loadManifest')->willReturn([
            'eager' => [],
            'deferred' => ['foo'],
        ]);
        $repository->method('shouldRecompile')->willReturn(true);

        $fooMock = new TestDeferredProvider($app);
        $repository->method('createProvider')
            ->with('foo')
            ->willReturn($fooMock);

        $app->expects($this->once())->method('addDeferredServices')->with([
            'foo.provides1' => 'foo',
            'foo.provides2' => 'foo',
        ]);

        $repository->load(['foo']);
    }

    public function testShouldRecompileReturnsCorrectValue()
    {
        $repo = new ProviderRepository(
            new Application(),
            $this->getMockBuilder('Illuminate\Filesystem\Filesystem')->getMock(),
            __DIR__ . '/services.php',
        );

        $this->assertTrue($repo->shouldRecompile(null, []));
        $this->assertTrue($repo->shouldRecompile(['providers' => ['foo']], ['foo', 'bar']));
        $this->assertFalse($repo->shouldRecompile(['providers' => ['foo']], ['foo']));
    }

    public function testLoadManifestReturnsParsedJSON()
    {
        $repo = new ProviderRepository(
            new Application(),
            $files = $this->getMockBuilder('Illuminate\Filesystem\Filesystem')
                ->onlyMethods(['exists', 'getRequire'])
                ->getMock(),
            __DIR__ . '/services.php',
        );

        $files->expects($this->once())
            ->method('exists')
            ->with(__DIR__ . '/services.php')
            ->willReturn(true);
        $files->expects($this->once())
            ->method('getRequire')
            ->with(__DIR__ . '/services.php')
            ->willReturn($array = ['users' => ['joe' => true], 'when' => []]);

        $this->assertEquals($array, $repo->loadManifest());
    }

    public function testWriteManifestStoresToProperLocation()
    {
        $repo = new ProviderRepository(
            new Application(),
            $files = $this->getMockBuilder('Illuminate\Filesystem\Filesystem')
                ->onlyMethods(['put'])
                ->getMock(),
            __DIR__ . '/services.php',
        );

        $files->expects($this->once())
            ->method('put')
            ->with(__DIR__ . '/services.php', '<?php return ' . var_export(['foo'], true) . ';');
        $result = $repo->writeManifest(['foo']);
        $this->assertEquals(['foo', 'when' => []], $result);
    }
}
