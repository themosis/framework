<?php

namespace Themosis\Core\Providers;

use Illuminate\Console\Scheduling\ScheduleFinishCommand;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Routing\Console\MiddlewareMakeCommand;
use Illuminate\Session\Console\SessionTableCommand;
use Illuminate\Support\ServiceProvider;
use Themosis\Auth\Console\AuthMakeCommand;
use Themosis\Core\Console\ConsoleMakeCommand;
use Themosis\Core\Console\CustomerTableCommand;
use Themosis\Core\Console\DownCommand;
use Themosis\Core\Console\FormMakeCommand;
use Themosis\Core\Console\HookMakeCommand;
use Themosis\Core\Console\KeyGenerateCommand;
use Themosis\Core\Console\MailMakeCommand;
use Themosis\Core\Console\ModelMakeCommand;
use Themosis\Core\Console\PackageDiscoverCommand;
use Themosis\Core\Console\PasswordResetTableCommand;
use Themosis\Core\Console\PluginInstallCommand;
use Themosis\Core\Console\ProviderMakeCommand;
use Themosis\Core\Console\RequestMakeCommand;
use Themosis\Core\Console\ResourceMakeCommand;
use Themosis\Core\Console\RouteCacheCommand;
use Themosis\Core\Console\RouteClearCommand;
use Themosis\Core\Console\RouteListCommand;
use Themosis\Core\Console\ServeCommand;
use Themosis\Core\Console\ThemeInstallCommand;
use Themosis\Core\Console\UpCommand;
use Themosis\Core\Console\VendorPublishCommand;
use Themosis\Core\Console\ViewClearCommand;
use Themosis\Core\Console\WidgetMakeCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Defer the loading of the provider.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Commands to register.
     *
     * @var array
     */
    protected $commands = [
        'Down' => 'command.down',
        'KeyGenerate' => 'command.key.generate',
        'Migrate' => 'command.migrate',
        'MigrateFresh' => 'command.migrate.fresh',
        'MigrateInstall' => 'command.migrate.install',
        'MigrateRefresh' => 'command.migrate.refresh',
        'MigrateReset' => 'command.migrate.reset',
        'MigrateRollback' => 'command.migrate.rollback',
        'MigrateStatus' => 'command.migrate.status',
        'PackageDiscover' => 'command.package.discover',
        'RouteCache' => 'command.route.cache',
        'RouteClear' => 'command.route.clear',
        'RouteList' => 'command.route.list',
        'ScheduleFinish' => ScheduleFinishCommand::class,
        'ScheduleRun' => ScheduleRunCommand::class,
        'Up' => 'command.up',
        'ViewClear' => 'command.view.clear'
    ];

    /**
     * Development commands to register.
     *
     * @var array
     */
    protected $devCommands = [
        'AuthMake' => 'command.auth.make',
        'ConsoleMake' => 'command.console.make',
        'ControllerMake' => 'command.controller.make',
        'CustomerTable' => 'command.customer.table',
        'FactoryMake' => 'command.factory.make',
        'FormMake' => 'command.form.make',
        'HookMake' => 'command.hook.make',
        'MailMake' => 'command.mail.make',
        'MiddlewareMake' => 'command.middleware.make',
        'MigrateMake' => 'command.migrate.make',
        'ModelMake' => 'command.model.make',
        'PasswordResetTable' => 'command.password.reset.table',
        'PluginInstall' => 'command.plugin.install',
        'ProviderMake' => 'command.provider.make',
        'RequestMake' => 'command.request.make',
        'ResourceMake' => 'command.resource.make',
        'SessionTable' => 'command.session.table',
        'ThemeInstall' => 'command.theme.install',
        'VendorPublish' => 'command.vendor.publish',
        'Serve' => 'command.serve',
        'WidgetMake' => 'command.widget.make',
    ];

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands,
            $this->devCommands
        ));
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     */
    protected function registerCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            call_user_func_array([$this, "register{$command}Command"], [$commands[$command]]);
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the make:auth command.
     *
     * @param string $abstract
     */
    protected function registerAuthMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new AuthMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:command command.
     *
     * @param string $abstract
     */
    protected function registerConsoleMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ConsoleMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:controller command.
     *
     * @param string $abstract
     */
    protected function registerControllerMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ControllerMakeCommand($app['files']);
        });
    }

    /**
     * Register the customer:table command.
     *
     * @param string $abstract
     */
    protected function registerCustomerTableCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new CustomerTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the down command.
     *
     * @param string $abstract
     */
    protected function registerDownCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new DownCommand();
        });
    }

    /**
     * Register the make:factory command.
     *
     * @param string $abstract
     */
    protected function registerFactoryMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new FactoryMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:form command.
     *
     * @param string $abstract
     */
    protected function registerFormMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new FormMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:hook command.
     *
     * @param string $abstract
     */
    protected function registerHookMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new HookMakeCommand($app['files']);
        });
    }

    /**
     * Register the key:generate command.
     *
     * @param string $abstract
     */
    protected function registerKeyGenerateCommand($abstract)
    {
        $this->app->singleton($abstract, function () {
            return new KeyGenerateCommand();
        });
    }

    /**
     * Register the make:mail command.
     *
     * @param string $abstract
     */
    protected function registerMailMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new MailMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:middleware command.
     *
     * @param string $abstract
     */
    protected function registerMiddlewareMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new MiddlewareMakeCommand($app['files']);
        });
    }

    /**
     * Register the migrate command.
     *
     * @param string $abstract
     */
    protected function registerMigrateCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new MigrateCommand($app['migrator']);
        });
    }

    /**
     * Register the migrate:fresh command.
     *
     * @param string $abstract
     */
    protected function registerMigrateFreshCommand($abstract)
    {
        $this->app->singleton($abstract, function () {
            return new FreshCommand();
        });
    }

    /**
     * Register the migrate:install command.
     *
     * @param string $abstract
     */
    protected function registerMigrateInstallCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new InstallCommand($app['migration.repository']);
        });
    }

    /**
     * Register the make:migration command.
     *
     * @param string $abstract
     */
    protected function registerMigrateMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];
            $composer = $app['composer'];

            return new MigrateMakeCommand($creator, $composer);
        });
    }

    /**
     * Register the migrate:refresh command.
     *
     * @param string $abstract
     */
    protected function registerMigrateRefreshCommand($abstract)
    {
        $this->app->singleton($abstract, function () {
            return new RefreshCommand();
        });
    }

    /**
     * Register the migrate:reset command.
     *
     * @param string $abstract
     */
    protected function registerMigrateResetCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ResetCommand($app['migrator']);
        });
    }

    /**
     * Register the migrate:rollback command.
     *
     * @param string $abstract
     */
    protected function registerMigrateRollbackCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new RollbackCommand($app['migrator']);
        });
    }

    /**
     * Register the migrate:status command.
     *
     * @param string $abstract
     */
    protected function registerMigrateStatusCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new StatusCommand($app['migrator']);
        });
    }

    /**
     * Register the make:model command.
     *
     * @param string $abstract
     */
    protected function registerModelMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Register the package:discover command.
     *
     * @param string $abstract
     */
    protected function registerPackageDiscoverCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new PackageDiscoverCommand();
        });
    }

    /**
     * Register the password:table command.
     *
     * @param string $abstract
     */
    protected function registerPasswordResetTableCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new PasswordResetTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the plugin:install command.
     *
     * @param string $abstract
     */
    protected function registerPluginInstallCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new PluginInstallCommand($app['files'], new \ZipArchive());
        });
    }

    /**
     * Register the make:provider command.
     *
     * @param string $abstract
     */
    protected function registerProviderMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ProviderMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:request command.
     *
     * @param string $abstract
     */
    protected function registerRequestMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new RequestMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:resource command.
     *
     * @param string $abstract
     */
    protected function registerResourceMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ResourceMakeCommand($app['files']);
        });
    }

    /**
     * Register the route:cache command.
     *
     * @param string $abstract
     */
    protected function registerRouteCacheCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new RouteCacheCommand($app['files']);
        });
    }

    /**
     * Register the route:clear command.
     *
     * @param string $abstract
     */
    protected function registerRouteClearCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new RouteClearCommand($app['files']);
        });
    }

    /**
     * Register the route:list command.
     *
     * @param string $abstract
     */
    protected function registerRouteListCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new RouteListCommand($app['router']);
        });
    }

    /**
     * Register the schedule:finish {id} command.
     */
    protected function registerScheduleFinishCommand()
    {
        $this->app->singleton(ScheduleFinishCommand::class);
    }

    /**
     * Register the schedule:run command.
     */
    protected function registerScheduleRunCommand()
    {
        $this->app->singleton(ScheduleRunCommand::class);
    }

    /**
     * Register the serve command.
     *
     * @param string $abstract
     */
    protected function registerServeCommand($abstract)
    {
        $this->app->singleton($abstract, function () {
            return new ServeCommand();
        });
    }

    /**
     * Register the session:table command.
     *
     * @param string $abstract
     */
    protected function registerSessionTableCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new SessionTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the theme:install command.
     *
     * @param string $abstract
     */
    protected function registerThemeInstallCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ThemeInstallCommand($app['files'], new \ZipArchive());
        });
    }

    /**
     * Register the up command.
     *
     * @param string $abstract
     */
    public function registerUpCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new UpCommand();
        });
    }

    /**
     * Register the vendor:publish command.
     *
     * @param string $abstract
     */
    protected function registerVendorPublishCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new VendorPublishCommand($app['files']);
        });
    }

    /**
     * Register the view:clear command.
     *
     * @param string $abstract
     */
    protected function registerViewClearCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ViewClearCommand($app['files']);
        });
    }

    /**
     * Register the make:widget command.
     *
     * @param string $abstract
     */
    protected function registerWidgetMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new WidgetMakeCommand($app['files']);
        });
    }

    /**
     * Return list of services provided.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_values($this->commands), array_values($this->devCommands));
    }
}
