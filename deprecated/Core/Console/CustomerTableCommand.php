<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class CustomerTableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'customer:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the customer database table';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Composer
     */
    protected $composer;

    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    public function handle()
    {
        $fullPath = $this->createBaseMigration();

        $this->files->put($fullPath, $this->files->get(__DIR__.'/stubs/customers.stub'));

        $this->info('Migration created successfully!');

        $this->composer->dumpAutoloads();
    }

    /**
     * Create a base migration file for the customer.
     *
     * @return string
     */
    protected function createBaseMigration()
    {
        $name = 'create_customers_table';
        $path = $this->laravel->databasePath('migrations');

        return $this->laravel['migration.creator']->create($name, $path);
    }
}
