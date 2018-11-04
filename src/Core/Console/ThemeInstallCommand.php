<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class ThemeInstallCommand extends Command
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'theme:install';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Install the themosis theme.';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var \ZipArchive
     */
    protected $zip;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->zip = new \ZipArchive();
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $name = $this->ask('Please choose a name for your theme!', 'themosis');

        $this->installTheme($name);
        $this->setupTheme($name);

        $this->info('Theme ['.$name.'] installed.');
    }

    /**
     * Install theme.
     *
     * @param string $name The theme name from user
     */
    protected function installTheme(string $name)
    {
        $this->info('Downloading theme...');

        $temp = storage_path('theme.zip');
        $this->files->put($temp, fopen($this->option('url'), 'r'));

        if (true !== $this->zip->open($temp)) {
            $this->error('Cannot open theme zip file.');
        }

        $this->zip->extractTo(themes_path());
        $this->zip->close();

        $this->files->move(
            themes_path($this->option('dir')),
            themes_path(str_replace(' ', '-', strtolower($name)))
        );
        $this->files->delete($temp);
    }

    /**
     * Setup basic theme information.
     *
     * @param string $name
     */
    protected function setupTheme(string $name)
    {
        $this->info('Setting up style.css file...');

        $themeName = ucfirst($name);
        $textdomain = str_replace(['-', ' '], '_', strtolower($name));

        $content = <<<STYLES
/*
Theme Name: $themeName
Theme URI: https://framework.themosis.com/
Author: Themosis
Author URI: https://www.themosis.com/
Description: The Themosis framework base theme.
Version: 1.0.0
License: GPL-2.0-or-later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: easy, organized, expressive.
Text Domain: $textdomain
*/
STYLES;

        $this->files->put(themes_path($name.'/style.css'), $content);
    }

    /**
     * Command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['dir', null, InputOption::VALUE_OPTIONAL, 'ZIP file base directory name.', 'theme-master'],
            ['url', null, InputOption::VALUE_OPTIONAL, 'Theme ZIP file URL.', 'https://github.com/themosis/theme/archive/master.zip']
        ];
    }
}
