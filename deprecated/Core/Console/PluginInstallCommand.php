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
        $this->setPluginHeaders($name, $headers);
        $this->setPluginRootFile($name);
        $this->setConfigurationFile($name, $headers);
        $this->setTranslationFile($name, $headers);
        $this->setProviders($name, $headers);

        $this->info('Plugin installed successfully.');
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
        $textdomain = $this->ask('Text domain:', trim($name, '\/-_'));
        $variable = strtoupper($this->ask('Domain variable:', 'PLUGIN_TD'));
        $prefix = $this->ask('Plugin prefix:', str_replace(['-', ' '], '_', $name));
        $namespace = $this->ask('PHP Namespace:', 'Tld\Domain\Plugin');

        return [
            'name' => ucwords(str_replace(['-', '_'], ' ', $name)),
            'description' => $description,
            'author' => $author,
            'text_domain' => $textdomain,
            'domain_var' => $variable,
            'plugin_prefix' => $prefix,
            'plugin_namespace' => $namespace,
            'plugin_id' => $name
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

        $this->zip->extractTo($this->path());
        $this->zip->close();

        $this->files->move(
            $this->path($this->option('dir')),
            $this->path($name)
        );

        $this->files->delete($this->temp);
    }

    /**
     * Setup the plugin headers.
     *
     * @param string $name
     * @param array  $headers
     */
    protected function setPluginHeaders(string $name, array $headers)
    {
        $this->info('Set plugin headers...');

        $path = $this->path($name.'/plugin-name.php');
        $handle = fopen($path, 'r');
        $content = [];

        while (($line = fgets($handle)) !== false) {
            $content[] = $this->parseLine($line, $headers);
        }

        fclose($handle);

        $this->files->put($path, implode('', $content), true);
    }

    /**
     * Parse file header line.
     *
     * @param string $line
     * @param array  $headers
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
        $this->info('Set plugin root file...');
        $this->files->move($this->path($name.'/plugin-name.php'), $this->path($name.'/'.$name.'.php'));
    }

    /**
     * Set the plugin configuration file.
     *
     * @param string $name
     * @param array  $headers
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function setConfigurationFile(string $name, array $headers)
    {
        $this->info('Set plugin configuration...');

        $prefix = trim($headers['plugin_prefix'], '\/_-');
        $from = $this->path($name.'/config/prefix_plugin.php');
        $to = $this->path($name.'/config/'.$prefix.'_plugin.php');

        $this->files->move($from, $to);
        $this->replaceFileContent($to, $headers);
    }

    /**
     * Set the plugin translation file.
     *
     * @param string $name
     * @param array  $headers
     */
    protected function setTranslationFile($name, array $headers)
    {
        $this->info('Set plugin translation file...');

        $textdomain = trim($headers['text_domain'], '\/ _-');
        $from = $this->path($name.'/languages/plugin-textdomain-en_US.po');
        $to = $this->path($name.'/languages/'.$textdomain.'-en_US.po');

        $this->files->move($from, $to);
    }

    /**
     * Set the content of default route provider.
     *
     * @param string $name
     * @param array  $headers
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function setProviders(string $name, array $headers)
    {
        $this->info('Set default route provider...');

        $this->replaceFileContent(
            $this->path($name.'/resources/Providers/RouteServiceProvider.php'),
            $headers
        );
    }

    /**
     * Replace file content with given headers values.
     *
     * @param string $path
     * @param array  $headers
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function replaceFileContent($path, array $headers)
    {
        $content = $this->files->get($path);
        $this->files->put($path, str_replace([
            'DummyNamespace',
            'DummyAutoload',
            'dummy_path'
        ], [
            $this->getNamespace($headers['plugin_namespace']),
            $this->getAutoloadNamespace($headers['plugin_namespace']),
            $this->getPluginPath()
        ], $content), true);
    }

    /**
     * Return the default namespace: "Tld\Domain\Plugin"
     *
     * @param string $default
     *
     * @return string
     */
    protected function getNamespace(string $default)
    {
        return str_replace("/", "\\", trim($default, '\/'));
    }

    /**
     * Return namespace for autoloading rule: "Tld\\Domain\\Plugin\\"
     *
     * @param string $default
     *
     * @return string
     */
    protected function getAutoloadNamespace(string $default)
    {
        return str_replace(["/", "\\"], ["\\", "\\\\"], trim($default, '\/'))."\\\\";
    }

    /**
     * Return the plugin base path.
     */
    protected function getPluginPath()
    {
        if ($this->option('mu')) {
            return 'muplugins_path';
        }

        return 'plugins_path';
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
     * Return the plugin path. Handle -mu case.
     *
     * @param string $path
     *
     * @return string
     */
    protected function path(string $path = '')
    {
        if ($this->option('mu')) {
            return muplugins_path($path);
        }

        return plugins_path($path);
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
            ['url', null, InputOption::VALUE_OPTIONAL, 'Plugin ZIP file URL.', 'https://github.com/themosis/plugin/archive/master.zip'],
            ['mu', null, InputOption::VALUE_NONE, 'Install as mu-plugin.']
        ];
    }
}
