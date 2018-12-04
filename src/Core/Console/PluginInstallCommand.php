<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Themosis\Core\Support\PluginHeaders;

class PluginInstallCommand extends Command
{
    use PluginHeaders;

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'plugin:install';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Install a Themosis plugin boilerplate';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var \ZipArchive
     */
    protected $zip;

    /**
     * Temporary plugin path for its zip package.
     *
     * @var string
     */
    protected $temp;

    public function __construct(Filesystem $files, \ZipArchive $zip)
    {
        parent::__construct();

        $this->files = $files;
        $this->zip = $zip;
        $this->temp = storage_path('plugin.zip');
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $name = $this->parseNameForDirectory($this->argument('name'));

        $headers = $this->generatePluginHeaders($name);
        $this->installPlugin($name);
        $this->setupPlugin($name, $headers);
        $this->setPluginRootFile($name);
    }

    /**
     * Generate plugin headers.
     *
     * @param string $name
     *
     * @return array
     */
    protected function generatePluginHeaders(string $name)
    {
        $description = $this->ask('Description:', '');
        $author = $this->ask('Author:', 'Themosis');
        $textdomain = $this->ask('Text domain:', 'plugin-textdomain');
        $variable = strtoupper($this->ask('Domain variable:', 'PLUGIN_TD'));
        $prefix = $this->ask('Plugin prefix:', 'tld_domain_plugin');

        return [
            'name' => ucwords(str_replace(['-', '_'], ' ', $name)),
            'description' => $description,
            'author' => $author,
            'text_domain' => $textdomain,
            'domain_var' => $variable,
            'plugin_prefix' => $prefix
        ];
    }

    /**
     * Install the plugin.
     *
     * @param string $name
     */
    protected function installPlugin(string $name)
    {
        $this->info('Downloading plugin...');

        $this->files->copy($this->option('url'), $this->temp);

        if (true !== $this->zip->open($this->temp)) {
            $this->error('Cannot open plugin zip file.');
            return;
        }

        $this->zip->extractTo(plugins_path());
        $this->zip->close();

        $this->files->move(
            plugins_path($this->option('dir')),
            plugins_path($name)
        );

        $this->files->delete($this->temp);
    }

    /**
     * Setup the plugin headers.
     *
     * @param string $name
     * @param array $headers
     */
    protected function setupPlugin(string $name, array $headers)
    {
        $this->info('Set plugin headers...');

        $path = plugins_path($name.'/plugin-name.php');
        $handle = fopen($path, 'r');
        $content = [];

        while (($line = fgets($handle)) !== false) {
            $content[] = $this->parseLine($line, $headers);
        }

        fclose($handle);

        $this->files->put($path, implode('', $content), true);
    }

    /**
     * Parse file line. Update plugin headers values.
     *
     * @param string $line
     * @param array $headers
     *
     * @return string
     */
    protected function parseLine(string $line, array $headers)
    {
        foreach ($this->headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*'.preg_quote($regex, '/').':(.*)$/mi', $line, $match)
                && $match[0]
                && isset($headers[$field])) {
                return preg_replace('/:\s?+.+\s?+/', ': '.$headers[$field], $match[0])."\n";
            }
        }

        return $line;
    }

    /**
     * Set the plugin root file name.
     *
     * @param string $name
     */
    protected function setPluginRootFile(string $name)
    {
        $this->files->move(plugins_path($name.'/plugin-name.php'), plugins_path($name.'/'.$name.'.php'));
    }

    /**
     * Parse the plugin name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseNameForDirectory(string $name)
    {
        return str_replace(' ', '-', trim(strtolower($name)));
    }

    /**
     * Command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The plugin name.']
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
            ['dir', null, InputOption::VALUE_OPTIONAL, 'ZIP file base directory name.', 'plugin-master'],
            ['url', null, InputOption::VALUE_OPTIONAL, 'Plugin ZIP file URL.', 'https://github.com/themosis/plugin/archive/master.zip']
        ];
    }
}
