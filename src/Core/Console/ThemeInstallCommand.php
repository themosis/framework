<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
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
    protected $description = 'Install a Themosis theme boilerplate';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var \ZipArchive
     */
    protected $zip;

    /**
     * @var string
     */
    protected $temp;

    public function __construct(Filesystem $files, \ZipArchive $zip)
    {
        parent::__construct();

        $this->files = $files;
        $this->zip = $zip;
        $this->temp = storage_path('theme.zip');
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $theme = strtolower($this->argument('name'));

        $this->installTheme($theme);
        $this->setupTheme($theme);

        if ($this->option('default')) {
            $this->setAsDefaultTheme($theme);
        }

        $this->info('Theme ['.$theme.'] installed.');
    }

    /**
     * Install theme.
     *
     * @param string $name The theme name from user
     */
    protected function installTheme(string $name)
    {
        $this->info('Downloading theme...');

        $this->files->put($this->temp, fopen($this->option('url'), 'r'));

        if (true !== $this->zip->open($this->temp)) {
            $this->error('Cannot open theme zip file.');
        }

        $this->zip->extractTo(themes_path());
        $this->zip->close();

        $this->files->move(
            themes_path($this->option('dir')),
            themes_path(str_replace(' ', '-', $name))
        );
        $this->files->delete($this->temp);
    }

    /**
     * Setup basic theme information.
     *
     * @param string $name
     */
    protected function setupTheme(string $name)
    {
        $this->info('Set [style.css] file...');

        $theme = ucwords(str_replace(['-', '_'], ' ', $name));
        $textdomain = str_replace(['-', ' '], '_', strtolower($name));

        $content = <<<STYLES
/*
Theme Name: $theme
Theme URI: https://framework.themosis.com/
Author: Themosis
Author URI: https://www.themosis.com/
Description: The Themosis framework base theme.
Version: 1.0.0
License: GPL-2.0-or-later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: easy, organized, expressive.
Text Domain: $textdomain
Domain Path: languages
*/
STYLES;

        $this->files->put(themes_path($name.'/style.css'), $content);
    }

    /**
     * Set default theme constant in main "wordpress.php" config file.
     *
     * @param string $name
     */
    protected function setAsDefaultTheme(string $name)
    {
        if (! file_exists($file = config_path('wordpress.php'))) {
            $this->warn('Cannot update default theme configuration.');

            return;
        }

        $this->info('Set default theme constant...');
        $lines = file($file);

        $content = array_map(function ($line) use ($name) {
            if (stristr($line, 'WP_DEFAULT_THEME')) {
                return "define('WP_DEFAULT_THEME', '{$name}');\r\n";
            }

            return $line;
        }, $lines);

        $this->files->put($file, implode('', $content));
    }

    /**
     * Command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The theme name.']
        ];
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
            ['url', null, InputOption::VALUE_OPTIONAL, 'Theme ZIP file URL.', 'https://github.com/themosis/theme/archive/master.zip'],
            ['default', null, InputOption::VALUE_OPTIONAL, 'Set default theme constant.', true]
        ];
    }
}
