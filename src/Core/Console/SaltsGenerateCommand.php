<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;


class SaltsGenerateCommand extends Command
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $signature = 'salts:generate';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Generate Wordpress keys&salts in your .env.';

    /**
     * Wordpress keys to generate salts for
     *
     * @var array
     */
    protected $keys = [
        "AUTH_KEY",
        "SECURE_AUTH_KEY",
        "LOGGED_IN_KEY",
        "NONCE_KEY",
        "AUTH_SALT",
        "SECURE_AUTH_SALT",
        "LOGGED_IN_SALT",
        "NONCE_SALT",
    ];

    /**
     * List of characters to be used in the salt generation
     *
     * @var string $chars
     */
    protected $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!"#$%&()*+,-./:;<=>?@[]^_`{|}~';

    /**
     * @var \RandomLib\Factory
     */
    protected $random;


    public function __construct(\RandomLib\Factory $random)
    {
        parent::__construct();
        $this->random = $random;
    }


    /**
     * Execute the command.
     */
    public function handle()
    {
        // Set env for each key
        foreach($this->keys as $key)
        {
            $this->callSilent('env:set',
            [
                'key' => $key,
                'value' => $this->salt(),
            ]);
        }

        $this->info('Successfully set WordPress keys & salts.');
    }


    /**
     * Generates 64 character salt string
     *
     * @return string
     */
    protected function salt(): string
    {
        return $this->random->getMediumStrengthGenerator()
            ->generateString(64, $this->chars);
    }
}
