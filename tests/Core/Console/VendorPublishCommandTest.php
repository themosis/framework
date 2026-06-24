<?php

namespace Themosis\Tests\Core\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Themosis\Core\Application;
use Themosis\Core\Console\VendorPublishCommand;

class VendorPublishCommandTest extends TestCase
{
    private string $from;
    private string $to;
    private Application $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new Application(sys_get_temp_dir());
        $this->app->instance('files', new Filesystem());

        $id = uniqid();
        $this->from = sys_get_temp_dir() . '/vp_from_' . $id;
        $this->to = sys_get_temp_dir() . '/vp_to_' . $id;
    }

    protected function tearDown(): void
    {
        ServiceProvider::$publishes = [];
        ServiceProvider::$publishGroups = [];

        $this->deletePath($this->from);
        $this->deletePath($this->to);

        parent::tearDown();
    }

    private function makeTester(): CommandTester
    {
        $command = new VendorPublishCommand(new Filesystem());
        $command->setLaravel($this->app);

        return new CommandTester($command);
    }

    private function registerPaths(array $paths): void
    {
        ServiceProvider::$publishes[PublishStubServiceProvider::class] = $paths;
    }

    public function testPublishesFileToDestination(): void
    {
        file_put_contents($this->from, 'hello');
        $dest = $this->to . '/file.txt';

        $this->registerPaths([$this->from => $dest]);

        $this->makeTester()->execute(['--provider' => PublishStubServiceProvider::class]);

        $this->assertFileExists($dest);
        $this->assertSame('hello', file_get_contents($dest));
    }

    public function testPublishesDirectoryRecursively(): void
    {
        mkdir($this->from . '/sub', 0755, true);
        mkdir($this->to, 0755, true);
        file_put_contents($this->from . '/top.txt', 'top');
        file_put_contents($this->from . '/sub/nested.txt', 'nested');

        $this->registerPaths([$this->from => $this->to]);

        $this->makeTester()->execute(['--provider' => PublishStubServiceProvider::class]);

        $this->assertFileExists($this->to . '/top.txt');
        $this->assertFileExists($this->to . '/sub/nested.txt');
        $this->assertSame('top', file_get_contents($this->to . '/top.txt'));
        $this->assertSame('nested', file_get_contents($this->to . '/sub/nested.txt'));
    }

    public function testSkipsExistingFileWithoutForce(): void
    {
        mkdir($this->from, 0755, true);
        mkdir($this->to, 0755, true);
        file_put_contents($this->from . '/file.txt', 'new');
        file_put_contents($this->to . '/file.txt', 'original');

        $this->registerPaths([$this->from => $this->to]);

        $this->makeTester()->execute(['--provider' => PublishStubServiceProvider::class]);

        $this->assertSame('original', file_get_contents($this->to . '/file.txt'));
    }

    public function testOverwritesExistingFileWithForce(): void
    {
        mkdir($this->from, 0755, true);
        mkdir($this->to, 0755, true);
        file_put_contents($this->from . '/file.txt', 'new');
        file_put_contents($this->to . '/file.txt', 'original');

        $this->registerPaths([$this->from => $this->to]);

        $this->makeTester()->execute([
            '--provider' => PublishStubServiceProvider::class,
            '--force' => true,
        ]);

        $this->assertSame('new', file_get_contents($this->to . '/file.txt'));
    }

    private function deletePath(string $path): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (is_file($path)) {
            unlink($path);

            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);
    }
}

class PublishStubServiceProvider extends ServiceProvider
{
    public function register(): void {}
}
