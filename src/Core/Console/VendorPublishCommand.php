<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\MountManager;

class VendorPublishCommand extends Command
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * The provider to publish.
     *
     * @var string
     */
    protected $provider;

    /**
     * The tags to publish.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * @var string
     */
    protected $signature = 'vendor:publish {--force : Overwrite any existing files.}
                    {--all : Publish assets for all service providers without prompt.}
                    {--provider= : The service provider that has assets you want to publish.}
                    {--tag=* : One or many tags that have assets you want to publish.}';

    /**
     * @var string
     */
    protected $description = 'Publish any publishable assets from vendor packages';

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->determineWhatShouldBePublished();

        foreach ($this->tags ?: [null] as $tag) {
            $this->publishTag($tag);
        }

        $this->info('Publishing complete.');
    }

    /**
     * Determine the provider of tag(s) to publish.
     */
    protected function determineWhatShouldBePublished()
    {
        if ($this->option('all')) {
            return;
        }

        list($this->provider, $this->tags) = [
            $this->option('provider'),
            (array) $this->option('tag'),
        ];

        if (! $this->provider && ! $this->tags) {
            $this->promptForProviderOrTag();
        }
    }

    /**
     * Prompt for which provider or tag to publish.
     */
    protected function promptForProviderOrTag()
    {
        $choice = $this->choice(
            "Which provider or tag's files would you like to publish?",
            $choices = $this->publishableChoices(),
        );

        if ($choice == $choices[0] || is_null($choice)) {
            return;
        }

        $this->parseChoice($choice);
    }

    /**
     * return the choices available via the prompt.
     *
     * @return array
     */
    protected function publishableChoices()
    {
        return array_merge(
            ['<comment>Publish files from all providers and tags listed below</comment>'],
            preg_filter('/^/', '<comment>Provider: </comment>', Arr::sort(ServiceProvider::publishableProviders())),
            preg_filter('/^/', '<comment>Tag: </comment>', Arr::sort(ServiceProvider::publishableGroups())),
        );
    }

    /**
     * Parse the answer that was given via the prompt.
     *
     * @param string $choice
     */
    protected function parseChoice($choice)
    {
        list($type, $value) = explode(': ', strip_tags($choice));

        if ($type === 'Provider') {
            $this->provider = $value;
        } elseif ($type === 'Tag') {
            $this->tags = [$value];
        }
    }

    /**
     * Publish the assets for a tag.
     *
     * @param string $tag
     */
    protected function publishTag($tag)
    {
        foreach ($this->pathsToPublish($tag) as $from => $to) {
            $this->publishItem($from, $to);
        }
    }

    /**
     * Get all the paths to publish.
     *
     * @param string $tag
     *
     * @return array
     */
    protected function pathsToPublish($tag)
    {
        return ServiceProvider::pathsToPublish($this->provider, $tag);
    }

    /**
     * Publish the given item from and to the given location.
     *
     * @param string $from
     * @param string $to
     */
    protected function publishItem($from, $to)
    {
        if ($this->files->isFile($from)) {
            $this->publishFile($from, $to);

            return;
        } elseif ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);

            return;
        }

        $this->error("Can't locate path: <{$from}>");
    }

    /**
     * Publish the file to the given path.
     *
     * @param string $from
     * @param string $to
     */
    protected function publishFile($from, $to)
    {
        if (! $this->files->exists($to) || $this->option('force')) {
            $this->createParentDirectory(dirname($to));
            $this->files->copy($from, $to);
            $this->status($from, $to, 'File');
        }
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param string $from
     * @param string $to
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    protected function publishDirectory($from, $to)
    {
        $this->moveManagedFiles(new MountManager([
            'from' => new \League\Flysystem\Filesystem(new LocalFilesystemAdapter($from)),
            'to' => new \League\Flysystem\Filesystem(new LocalFilesystemAdapter($to)),
        ]));

        $this->status($from, $to, 'Directory');
    }

    /**
     * Move all the files in the given MountManager.
     *
     * @param \League\Flysystem\MountManager $manager
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    protected function moveManagedFiles($manager)
    {
        foreach ($manager->listContents('from://', true) as $file) {
            if ($file['type'] === 'file' && ( ! $manager->has('to://' . $file['path']) || $this->option('force'))) {
                $manager->write(
                    preg_replace('{^from://}', 'to://', $file['path']),
                    $manager->read($file['path'])
                );
            }
        }
    }

    /**
     * Create the directory to house the published files if needed.
     *
     * @param string $directory
     */
    protected function createParentDirectory($directory)
    {
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Write a status message to the console.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path(), '', realpath($from));
        $to = str_replace(base_path(), '', realpath($to));

        $this->line(
            "<info>Copied {$type}</info> <comment>[{$from}]</comment> <info>To</info> <comment>[{$to}]</comment>",
        );
    }
}
