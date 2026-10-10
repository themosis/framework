<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\Providers\ComposerServiceProvider;
use Themosis\Metabox\Contracts\MetaboxManagerInterface;
use Themosis\Metabox\MetaboxServiceProvider;
use Themosis\Page\PageServiceProvider;

class DeferredProvidersTest extends TestCase
{
    public static function deferredProviders(): array
    {
        return [
            'metabox' => [MetaboxServiceProvider::class, ['metabox', MetaboxManagerInterface::class]],
            'page' => [PageServiceProvider::class, ['page']],
            'composer' => [ComposerServiceProvider::class, ['composer']],
        ];
    }

    #[DataProvider('deferredProviders')]
    public function testProviderIsDeferredAndDeclaresItsServices(string $class, array $services)
    {
        $provider = new $class(new Application());

        $this->assertTrue($provider->isDeferred(), "{$class} should be deferred.");
        $this->assertEqualsCanonicalizing($services, $provider->provides());
    }
}
