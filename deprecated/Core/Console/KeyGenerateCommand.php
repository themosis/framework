<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Encryption\Encrypter;

class KeyGenerateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * Name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'key:generate
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * Console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        if ($this->option('show')) {
            $this->line('<comment>'.$key.'</comment>');

            return;
        }

        // Next, we will replace the application key in the environment file so it is
        // automatically setup for this developer. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (! $this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->laravel['config']['app.key'] = $key;

        $this->info("Application key [$key] set successfully.");
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return 'base64:'.base64_encode(Encrypter::generateKey($this->laravel['config']['app.cipher']));
    }

    /**
     * Set the application key in the environment file.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function setKeyInEnvironmentFile(string $key)
    {
        $currentKey = $this->laravel['config']['app.key'];

        if (0 !== strlen($currentKey) && (! $this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write new environment file with the given key.
     *
     * @param string $key
     */
    protected function writeNewEnvironmentFileWith(string $key)
    {
        file_put_contents(
            $this->laravel->environmentFilePath(),
            preg_replace(
                $this->keyReplacementPattern(),
                'APP_KEY='.$key,
                file_get_contents($this->laravel->environmentFilePath())
            )
        );
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('='.$this->laravel['config']['app.key'], '/');

        return "/^APP_KEY{$escaped}/m";
    }
}
