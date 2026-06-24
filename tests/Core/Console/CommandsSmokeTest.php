<?php

namespace Themosis\Tests\Core\Console;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Themosis\Core\Application;
use Themosis\Core\Console\ConfigCacheCommand;
use Themosis\Core\Console\EventCacheCommand;
use Themosis\Core\Console\EventMakeCommand;
use Themosis\Core\Console\KeyGenerateCommand;
use Themosis\Core\Console\ProviderMakeCommand;
use Themosis\Core\Console\RouteCacheCommand;
use Themosis\Core\Console\StubPublishCommand;
use Themosis\Core\Console\VendorPublishCommand;

class CommandsSmokeTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = realpath(__DIR__ . '/../../');
    }

    private function makeApplication(): Application
    {
        $app = new Application($this->basePath);
        $app->instance('files', new Filesystem());

        return $app;
    }

    /**
     * @dataProvider commandProvider
     */
    public function testCommandInstantiatesAndHasNameAndDescription(
        string $class,
        array $args,
    ): void {
        $command = new $class(...$args);
        $command->setLaravel($this->makeApplication());

        $this->assertInstanceOf(SymfonyCommand::class, $command);
        $this->assertNotEmpty($command->getName(), "{$class} must have a non-empty name");
        $this->assertNotEmpty($command->getDescription(), "{$class} must have a non-empty description");
    }

    public static function commandProvider(): array
    {
        $files = new Filesystem();

        return [
            'ProviderMakeCommand (generator)' => [ProviderMakeCommand::class, [$files]],
            'VendorPublishCommand (publish)' => [VendorPublishCommand::class, [$files]],
            'EventMakeCommand (generator)' => [EventMakeCommand::class, [$files]],
            'StubPublishCommand (no-arg command)' => [StubPublishCommand::class, []],
            'KeyGenerateCommand (no-arg command)' => [KeyGenerateCommand::class, []],
            'EventCacheCommand (no-arg command)' => [EventCacheCommand::class, []],
            'ConfigCacheCommand (filesystem command)' => [ConfigCacheCommand::class, [$files]],
            'RouteCacheCommand (filesystem command)' => [RouteCacheCommand::class, [$files]],
        ];
    }
}
