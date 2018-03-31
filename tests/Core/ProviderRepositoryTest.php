<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;

class ProviderRepositoryTest extends TestCase
{
    public function testServicesAreRegisteredWhenManifestIsNotRecompiled()
    {
        $app = $this->getMockBuilder('Themosis\Core\Application')
            ->setMethods(['register', 'addDeferredServices', 'runningInConsole'])
            ->getMock();
        $repository = $this->getMockBuilder('Themosis\Core\ProviderRepository')
            ->setConstructorArgs([
                $app,
                $this->getMockBuilder('Illuminate\Filesystem\Filesystem')->getMock(),
                __DIR__.'/services.php'
            ])
            ->setMethods([
                'loadManifest',
                'shouldRecompile',
                'compileManifest',
                'createProvider'
            ])
            ->getMock();
        $repository->expects($this->once())
            ->method('loadManifest')
            ->willReturn([
                'eager' => ['foo'],
                'deferred' => ['deferred'],
                'providers' => ['providers'],
                'when' => []
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
            ->setMethods(['register', 'addDeferredServices', 'runningInConsole'])
            ->getMock();
        $repository = $this->getMockBuilder('Themosis\Core\ProviderRepository')
            ->setConstructorArgs([
                $app,
                $this->getMockBuilder('Illuminate\Filesystem\Filesystem')->getMock(),
                __DIR__.'/services.php'
            ])
            ->setMethods([
                'loadManifest',
                'shouldRecompile',
                'createProvider'
            ])
            ->getMock();

        $repository->expects($this->once())
            ->method('loadManifest')
            ->willReturn([
                'eager' => [],
                'deferred' => ['deferred'],
            ]);
        $repository->expects($this->once())->method('shouldRecompile')->willReturn(true);

        // foo mock is just a deferred provider
        $repository->expects($this->once())
            ->method('createProvider')
            ->with('foo')
            ->willReturn($fooMock = $this->getMockBuilder('stdClass')
                ->setMethods(['isDeferred', 'provides', 'when'])
                ->getMock());
        $fooMock->expects($this->once())->method('isDeferred')->willReturn(true);
        $fooMock->expects($this->once())->method('provides')->willReturn(['foo.provides1', 'foo.provides2']);
        $fooMock->expects($this->once())->method('when')->willReturn([]);

        // bar mock is added to eagers since it's not reserved
        /*$repository
            ->method('createProvider')
            ->with('bar')
            ->willReturn($barMock = $this->getMockBuilder('Illuminate\Support\ServiceProvider')
                ->setConstructorArgs([$app])
                ->setMethods(['isDeferred'])
                ->getMock());*/

        //$app->expects($this->once())->method('register')->with('bar');

        $app->expects($this->any())->method('runningInConsole')->willReturn(false);
        $app->expects($this->once())->method('addDeferredServices')->with([
            'foo.provides1' => 'foo',
            'foo.provides2' => 'foo'
        ]);

        $repository->load(['foo']);
    }
}
