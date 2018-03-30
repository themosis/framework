<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;

class ProviderRepositoryTest extends TestCase
{
    public function testServicesAreRegisteredWhenManifestIsNotRecompiled()
    {
        $app = $this->getMockBuilder('Themosis\Core\Application')
            ->setMethods(['register', 'addDeferredServices'])
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

        $repository->load([]);
    }
}
