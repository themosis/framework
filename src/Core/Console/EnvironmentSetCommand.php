<?php

namespace Themosis\Core\Console;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;


/**
 * @source https://github.com/imliam/laravel-env-set-command/blob/be51ce036f8d5c935a5bb2d21768831a4efd0d17/src/EnvironmentSetCommand.php
 */
class EnvironmentSetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:set {key} {value?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set and save an environment variable in the .env file';

    /**
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get key & value
        try
        {
            [$key, $value] = $this->getKeyValue();
        }
        catch(\InvalidArgumentException $e)
        {
            return $this->error($e->getMessage());
        }

        // add strings to value
        $value = "'" . $value . "'";

        // get .env file
        $env_file_path = app()->environmentPath();
        if( strpos($env_file_path, '.env') === false )
            $env_file_path .= DS.'.env';

        // ensure .env is present and writeable
        if(
            !$this->files->exists($env_file_path)
            || !$this->files->isReadable($env_file_path)
            || !$this->files->isWritable($env_file_path)
        )
            throw new \Exception("Env file doesn't exist or is not writeable.");
            
        $contents = file_get_contents($env_file_path);
        
        // if already exists, replace
        $old_value = $this->getOldValue($contents, $key);
        if( $old_value )
        {
            $old_key_val = $this->getOldKeyValue($contents, $key);
            $contents = str_replace($old_key_val, "{$key}={$value}", $contents);
            $this->writeFile($env_file_path, $contents);

            return $this->info("Environment variable with key {$key} has been changed from {$old_value} to {$value}");
        }

        // add newline if there's not already one
        if( !substr($contents,-1) == "\n" )
            $contents .= "\n";
        
        // add new value
        $contents .= "{$key}={$value}\n";
        $this->writeFile($env_file_path, $contents);

        return $this->info("A new environment variable with key {$key} has been set to {$value}");
    }

    /**
     * Overwrite the contents of a file.
     *
     * @param string $path
     * @param string $contents
     * @return boolean
     */
    protected function writeFile(string $path, string $contents): bool
    {
        $file = fopen($path, 'w');
        fwrite($file, $contents);

        return fclose($file);
    }

    /**
     * Get the old value of a given key from an environment file.
     *
     * @param string $env_file
     * @param string $key
     * @return string
     */
    protected function getOldValue(string $env_file, string $key): string
    {
        $key_val = $this->getOldKeyValue($env_file, $key);
        if(!$key_val)
            return '';

        // get key
        $val = explode('=', $key_val)[1];
        // remove space from start, if there is any
        return ltrim($val, ' ');
    }

    protected function getOldKeyValue(string $env_file, string $key): ?string
    {
        // Match the given key at the beginning of a line
        preg_match("/^{$key}[ ]?=[^\r\n]*/m", $env_file, $matches);

        // if not found
        if( !count($matches) )
            return null;

        return $matches[0];
    }

    /**
     * Determine what the supplied key and value is from the current command.
     *
     * @return array
     */
    protected function getKeyValue(): array
    {
        $key = $this->argument('key');
        $value = $this->argument('value');

        // if key and value weren't given seperately, split it from =
        if(!$value)
        {
            $parts = explode('=', $key, 2);

            if (count($parts) !== 2)
                throw new InvalidArgumentException('No value was set');

            $key = $parts[0];
            $value = $parts[1];
        }

        // throw error if invalid key
        if( !$this->isValidKey($key) )
            throw new InvalidArgumentException('Invalid argument key');

        // add strings if it contains spaces
        if( !is_bool(strpos($value, ' ')) )
            $value = '"' . $value . '"';

        return [strtoupper($key), $value];
    }

    /**
     * Check if a given string is valid as an environment variable key.
     *
     * @param string $key
     * @return boolean
     */
    protected function isValidKey(string $key): bool
    {
        if(str_contains($key, '=') )
            throw new InvalidArgumentException("Environment key should not contain '='");

        if(!preg_match('/^[a-zA-Z_]+$/', $key))
            throw new InvalidArgumentException('Invalid environment key. Only use letters and underscores');

        return true;
    }
}
