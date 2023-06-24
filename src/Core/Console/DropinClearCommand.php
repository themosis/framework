<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Themosis\Core\Dropins\WordPressDropins;

class DropinClearCommand extends Command
{
    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'dropin:clear
                            {--file= : The drop-in file to clear}
                            {--all : Clear all drop-in files.}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Clear defined or all WordPress drop-in files';

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * The dropin to clear.
     *
     * @var string
     */
    private $dropin;

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
        $this->determineWhatShouldBeCleared();

        foreach ($this->dropin ? [$this->dropin] : [null] as $file) {
            $this->clearDropin($file);
        }

        $this->info('Drop-in files cleared.');
    }

    /**
     * Determine the drop-in files to clear.
     */
    private function determineWhatShouldBeCleared()
    {
        if ($this->option('all')) {
            return;
        }

        $this->dropin = $this->option('file');

        if (! $this->dropin) {
            $this->promptForDropinFiles();
        }
    }

    /**
     * Prompt for drop-in files to clear.
     */
    private function promptForDropinFiles()
    {
        $choice = $this->choice(
            'Which drop-in file(s) would you like to clear?',
            $choices = $this->dropinChoices(),
        );

        if ($choice == $choices[0] || is_null($choice)) {
            return;
        }

        $this->dropin = strip_tags($choice);
    }

    /**
     * Return the available choices via the prompt.
     *
     * @return array
     */
    private function dropinChoices()
    {
        return array_merge(
            ['<comment>Clear all drop-in files</comment>'],
            preg_filter(
                '/([a-zA-Z-_]+)/',
                '<comment>${1}</comment>',
                array_keys(WordPressDropins::publishableDropins()),
            ),
        );
    }

    /**
     * Clear dropin file(s).
     *
     * @param  string|null  $file
     */
    private function clearDropin($file)
    {
        $paths = WordPressDropins::dropinPaths($file);

        foreach ($paths as $path) {
            if ($this->files->exists($fullpath = content_path($path))) {
                $this->info("Drop-in [{$path}] cleared.");
                $this->files->delete($fullpath);
            }
        }
    }
}
