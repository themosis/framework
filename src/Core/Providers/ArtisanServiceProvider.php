<?php

namespace Themosis\Core\Providers;

use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Cache\Console\CacheTableCommand;
use Illuminate\Cache\Console\ClearCommand as CacheClearCommand;
use Illuminate\Cache\Console\ForgetCommand as CacheForgetCommand;
use Illuminate\Console\Scheduling\ScheduleFinishCommand;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Console\Scheduling\ScheduleTestCommand;
use Illuminate\Console\Scheduling\ScheduleWorkCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Console\DbCommand;
use Illuminate\Database\Console\DumpCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Database\Console\WipeCommand;
use Illuminate\Notifications\Console\NotificationTableCommand;
use Illuminate\Queue\Console\BatchesTableCommand;
use Illuminate\Queue\Console\ClearCommand as QueueClearCommand;
use Illuminate\Queue\Console\FailedTableCommand;
use Illuminate\Queue\Console\FlushFailedCommand as FlushFailedQueueCommand;
use Illuminate\Queue\Console\ForgetFailedCommand as ForgetFailedQueueCommand;
use Illuminate\Queue\Console\ListenCommand as ListenQueueCommand;
use Illuminate\Queue\Console\ListFailedCommand as ListFailedQueueCommand;
use Illuminate\Queue\Console\PruneBatchesCommand as PruneBatchesQueueCommand;
use Illuminate\Queue\Console\RestartCommand as RestartQueueCommand;
use Illuminate\Queue\Console\RetryBatchCommand as QueueRetryBatchCommand;
use Illuminate\Queue\Console\RetryCommand as RetryQueueCommand;
use Illuminate\Queue\Console\TableCommand;
use Illuminate\Queue\Console\WorkCommand as WorkQueueCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Routing\Console\MiddlewareMakeCommand;
use Illuminate\Session\Console\SessionTableCommand;
use Illuminate\Support\ServiceProvider;
use Themosis\Auth\Console\AuthMakeCommand;
use Themosis\Core\Console\CastMakeCommand;
use Themosis\Core\Console\ChannelMakeCommand;
use Themosis\Core\Console\ClearCompiledCommand;
use Themosis\Core\Console\ComponentMakeCommand;
use Themosis\Core\Console\ConfigCacheCommand;
use Themosis\Core\Console\ConfigClearCommand;
use Themosis\Core\Console\ConsoleMakeCommand;
use Themosis\Core\Console\CustomerTableCommand;
use Themosis\Core\Console\DownCommand;
use Themosis\Core\Console\DropinClearCommand;
use Themosis\Core\Console\EnvironmentCommand;
use Themosis\Core\Console\EventCacheCommand;
use Themosis\Core\Console\EventClearCommand;
use Themosis\Core\Console\EventGenerateCommand;
use Themosis\Core\Console\EventListCommand;
use Themosis\Core\Console\EventMakeCommand;
use Themosis\Core\Console\ExceptionMakeCommand;
use Themosis\Core\Console\FormMakeCommand;
use Themosis\Core\Console\HookMakeCommand;
use Themosis\Core\Console\JobMakeCommand;
use Themosis\Core\Console\KeyGenerateCommand;
use Themosis\Core\Console\ListenerMakeCommand;
use Themosis\Core\Console\MailMakeCommand;
use Themosis\Core\Console\ModelMakeCommand;
use Themosis\Core\Console\NotificationMakeCommand;
use Themosis\Core\Console\ObserverCommand;
use Themosis\Core\Console\OptimizeClearCommand;
use Themosis\Core\Console\OptimizeCommand;
use Themosis\Core\Console\PackageDiscoverCommand;
use Themosis\Core\Console\PasswordResetTableCommand;
use Themosis\Core\Console\PluginInstallCommand;
use Themosis\Core\Console\PolicyMakeCommand;
use Themosis\Core\Console\ProviderMakeCommand;
use Themosis\Core\Console\PublishFuturePostCommand;
use Themosis\Core\Console\RequestMakeCommand;
use Themosis\Core\Console\ResourceMakeCommand;
use Themosis\Core\Console\RouteCacheCommand;
use Themosis\Core\Console\RouteClearCommand;
use Themosis\Core\Console\RouteListCommand;
use Themosis\Core\Console\RuleMakeCommand;
use Themosis\Core\Console\SaltsGenerateCommand;
use Themosis\Core\Console\ServeCommand;
use Themosis\Core\Console\StorageLinkCommand;
use Themosis\Core\Console\StubPublishCommand;
use Themosis\Core\Console\TestMakeCommand;
use Themosis\Core\Console\ThemeInstallCommand;
use Themosis\Core\Console\UpCommand;
use Themosis\Core\Console\VendorPublishCommand;
use Themosis\Core\Console\ViewCacheCommand;
use Themosis\Core\Console\ViewClearCommand;
use Themosis\Core\Console\WidgetMakeCommand;

class ArtisanServiceProvider extends ServiceProvider implements DeferrableProvider
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
        'CacheClear' => 'command.cache.clear',
        'CacheForget' => 'command.cache.forget',
        'ClearCompiled' => 'command.clear-compiled',
        'ClearResets' => 'command.auth.resets.clear',
        'ConfigCache' => 'command.config.cache',
        'ConfigClear' => 'command.config.clear',
        'Db' => DbCommand::class,
        'DbWipe' => 'command.db.wipe',
        'Down' => 'command.down',
        'DropinClear' => 'command.dropin.clear',
        'Environment' => 'command.environment',
        'EventCache' => 'command.event.cache',
        'EventClear' => 'command.event.clear',
        'EventList' => 'command.event.list',
        'KeyGenerate' => 'command.key.generate',
        'Optimize' => 'command.optimize',
        'OptimizeClear' => 'command.optimize.clear',
        'PackageDiscover' => 'command.package.discover',
        'PublishFuturePost' => 'command.publish.future-post',
        'QueueClear' => 'command.queue.clear',
        'QueueFailed' => 'command.queue.failed',
        'QueueFlush' => 'command.queue.flush',
        'QueueForget' => 'command.queue.forget',
        'QueueListen' => 'command.queue.listen',
        'QueuePruneBatches' => 'command.queue.prune-batches',
        'QueueRestart' => 'command.queue.restart',
        'QueueRetry' => 'command.queue.retry',
        'QueueRetryBatch' => 'command.queue.retry-batch',
        'QueueWork' => 'command.queue.work',
        'RouteCache' => 'command.route.cache',
        'RouteClear' => 'command.route.clear',
        'RouteList' => 'command.route.list',
        'SaltsGenerate' => 'command.salts.generate',
        'ScheduleFinish' => ScheduleFinishCommand::class,
        'ScheduleList' => ScheduleListCommand::class,
        'ScheduleRun' => ScheduleRunCommand::class,
        'ScheduleTest' => ScheduleTestCommand::class,
        'ScheduleWork' => ScheduleWorkCommand::class,
        'SchemaDump' => 'command.schema.dump',
        'Seed' => 'command.seed',
        'StorageLink' => 'command.storage.link',
        'Up' => 'command.up',
        'ViewCache' => 'command.view.cache',
        'ViewClear' => 'command.view.clear',
    ];

    /**
     * Development commands to register.
     *
     * @var array
     */
    protected $devCommands = [
        //'AuthMake' => 'command.auth.make',
        'CacheTable' => 'command.cache.table',
        'CastMake' => 'command.cast.make',
        'ChannelMake' => 'command.channel.make',
        'ComponentMake' => 'command.component.make',
        'ConsoleMake' => 'command.console.make',
        'ControllerMake' => 'command.controller.make',
        'CustomerTable' => 'command.customer.table',
        'EventGenerate' => 'command.event.generate',
        'EventMake' => 'command.event.make',
        'ExceptionMake' => 'command.exception.make',
        'FactoryMake' => 'command.factory.make',
        'FormMake' => 'command.form.make',
        'HookMake' => 'command.hook.make',
        'JobMake' => 'command.job.make',
        'ListenerMake' => 'command.listener.make',
        'MailMake' => 'command.mail.make',
        'MiddlewareMake' => 'command.middleware.make',
        'ModelMake' => 'command.model.make',
        'NotificationMake' => 'command.notification.make',
        'NotificationTable' => 'command.notification.table',
        'ObserverMake' => 'command.observer.make',
        'PasswordResetTable' => 'command.password.reset.table',
        'PluginInstall' => 'command.plugin.install',
        'PolicyMake' => 'command.policy.make',
        'ProviderMake' => 'command.provider.make',
        'QueueFailedTable' => 'command.queue.failed-table',
        'QueueTable' => 'command.queue.table',
        'QueueBatchesTable' => 'command.queue.batches-table',
        'RequestMake' => 'command.request.make',
        'ResourceMake' => 'command.resource.make',
        'RuleMake' => 'command.rule.make',
        'SeederMake' => 'command.seeder.make',
        'SessionTable' => 'command.session.table',
        'Serve' => 'command.serve',
        'StubPublish' => 'command.stub.publish',
        'TestMake' => 'command.test.make',
        'ThemeInstall' => 'command.theme.install',
        'VendorPublish' => 'command.vendor.publish',
        'WidgetMake' => 'command.widget.make',
    ];

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands,
            $this->devCommands,
        ));
    }

    /**
     * Register the given commands.
     */
    protected function registerCommands(array $commands)
    {
        foreach ($commands as $command => $alias) {
            $this->{"register{$command}Command"}($alias);
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the make:auth command.
     *
     * @param  string  $alias
     */
    protected function registerAuthMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new AuthMakeCommand($app['files']);
        });
    }

    /**
     * Register the cache:clear command.
     *
     * @param  string  $alias
     */
    protected function registerCacheClearCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new CacheClearCommand($app['cache'], $app['files']);
        });
    }

    /**
     * Register the cache:forget command.
     *
     * @param  string  $alias
     */
    protected function registerCacheForgetCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new CacheForgetCommand($app['cache']);
        });
    }

    /**
     * Register the cache:table command.
     *
     * @param  string  $alias
     */
    protected function registerCacheTableCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new CacheTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the make:cast command.
     *
     * @param  string  $alias
     */
    protected function registerCastMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new CastMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:channel command.
     *
     * @param  string  $alias
     */
    protected function registerChannelMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ChannelMakeCommand($app['files']);
        });
    }

    /**
     * Register the clear-compiled command.
     *
     * @param  string  $alias
     */
    protected function registerClearCompiledCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new ClearCompiledCommand();
        });
    }

    protected function registerClearResetsCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new ClearResetsCommand();
        });
    }

    /**
     * Register the make:component command.
     *
     * @param  string  $alias
     */
    protected function registerComponentMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ComponentMakeCommand($app['files']);
        });
    }

    /**
     * Register the config:cache command.
     *
     * @param  string  $alias
     */
    protected function registerConfigCacheCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ConfigCacheCommand($app['files']);
        });
    }

    /**
     * Register the config:clear command.
     *
     * @param  string  $alias
     */
    protected function registerConfigClearCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ConfigClearCommand($app['files']);
        });
    }

    /**
     * Register the make:command command.
     *
     * @param  string  $alias
     */
    protected function registerConsoleMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ConsoleMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:controller command.
     *
     * @param  string  $alias
     */
    protected function registerControllerMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ControllerMakeCommand($app['files']);
        });
    }

    /**
     * Register the customer:table command.
     *
     * @param  string  $alias
     */
    protected function registerCustomerTableCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new CustomerTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the db command.
     */
    protected function registerDbCommand()
    {
        $this->app->singleton(DbCommand::class);
    }

    /**
     * Register the db:wipe command.
     *
     * @param  string  $alias
     */
    protected function registerDbWipeCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new WipeCommand();
        });
    }

    /**
     * Register the down command.
     *
     * @param  string  $alias
     */
    protected function registerDownCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new DownCommand();
        });
    }

    /**
     * Register the dropin:clear command.
     *
     * @param  string  $alias
     */
    protected function registerDropinClearCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new DropinClearCommand($app['files']);
        });
    }

    /**
     * Register the env command.
     *
     * @param  string  $alias
     */
    protected function registerEnvironmentCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new EnvironmentCommand();
        });
    }

    /**
     * Register the event:cache command.
     *
     * @param  string  $alias
     */
    protected function registerEventCacheCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new EventCacheCommand();
        });
    }

    /**
     * Register the event:clear command.
     *
     * @param  string  $alias
     */
    protected function registerEventClearCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new EventClearCommand($app['files']);
        });
    }

    /**
     * Register the event:generate command.
     *
     * @param  string  $alias
     */
    protected function registerEventGenerateCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new EventGenerateCommand();
        });
    }

    /**
     * Register the event:list command.
     *
     * @param  string  $alias
     */
    protected function registerEventListCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new EventListCommand();
        });
    }

    /**
     * Register the make:event command.
     *
     * @param  string  $alias
     */
    protected function registerEventMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new EventMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:exception command.
     *
     * @param  string  $alias
     */
    protected function registerExceptionMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ExceptionMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:factory command.
     *
     * @param  string  $alias
     */
    protected function registerFactoryMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new FactoryMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:form command.
     *
     * @param  string  $alias
     */
    protected function registerFormMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new FormMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:hook command.
     *
     * @param  string  $alias
     */
    protected function registerHookMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new HookMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:job command.
     *
     * @param  string  $alias
     */
    protected function registerJobMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new JobMakeCommand($app['files']);
        });
    }

    /**
     * Register the key:generate command.
     *
     * @param  string  $alias
     */
    protected function registerKeyGenerateCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new KeyGenerateCommand();
        });
    }

    /**
     * Register the make:listener command.
     *
     * @param  string  $alias
     */
    protected function registerListenerMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ListenerMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:mail command.
     *
     * @param  string  $alias
     */
    protected function registerMailMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new MailMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:middleware command.
     *
     * @param  string  $alias
     */
    protected function registerMiddlewareMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new MiddlewareMakeCommand($app['files']);
        });
    }

    /**
     * Register the migrate command.
     *
     * @param  string  $alias
     */
    protected function registerMigrateCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new MigrateCommand($app['migrator']);
        });
    }

    /**
     * Register the migrate:fresh command.
     *
     * @param  string  $alias
     */
    protected function registerMigrateFreshCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new FreshCommand();
        });
    }

    /**
     * Register the migrate:install command.
     *
     * @param  string  $alias
     */
    protected function registerMigrateInstallCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new InstallCommand($app['migration.repository']);
        });
    }

    /**
     * Register the make:migration command.
     *
     * @param  string  $alias
     */
    protected function registerMigrateMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
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
     * @param  string  $alias
     */
    protected function registerMigrateRefreshCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new RefreshCommand();
        });
    }

    /**
     * Register the migrate:reset command.
     *
     * @param  string  $alias
     */
    protected function registerMigrateResetCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ResetCommand($app['migrator']);
        });
    }

    /**
     * Register the migrate:rollback command.
     *
     * @param  string  $alias
     */
    protected function registerMigrateRollbackCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new RollbackCommand($app['migrator']);
        });
    }

    /**
     * Register the migrate:status command.
     *
     * @param  string  $alias
     */
    protected function registerMigrateStatusCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new StatusCommand($app['migrator']);
        });
    }

    /**
     * Register the make:model command.
     *
     * @param  string  $alias
     */
    protected function registerModelMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:notification command.
     *
     * @param  string  $alias
     */
    protected function registerNotificationMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new NotificationMakeCommand($app['files']);
        });
    }

    /**
     * Register the notification:table command.
     *
     * @param  string  $alias
     */
    protected function registerNotificationTableCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new NotificationTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the make:observer command.
     *
     * @param  string  $alias
     */
    protected function registerObserverMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ObserverCommand($app['files']);
        });
    }

    /**
     * Register the optimize command.
     *
     * @param  string  $alias
     */
    protected function registerOptimizeCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new OptimizeCommand();
        });
    }

    /**
     * Register the optimize:clear command.
     *
     * @param  string  $alias
     */
    protected function registerOptimizeClearCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new OptimizeClearCommand();
        });
    }

    /**
     * Register the package:discover command.
     *
     * @param  string  $alias
     */
    protected function registerPackageDiscoverCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new PackageDiscoverCommand();
        });
    }

    protected function registerPublishFuturePostCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new PublishFuturePostCommand();
        });
    }

    /**
     * Register the password:table command.
     *
     * @param  string  $alias
     */
    protected function registerPasswordResetTableCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new PasswordResetTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the plugin:install command.
     *
     * @param  string  $alias
     */
    protected function registerPluginInstallCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new PluginInstallCommand($app['files'], new \ZipArchive());
        });
    }

    /**
     * Register the make:policy command.
     *
     * @param  string  $alias
     */
    protected function registerPolicyMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new PolicyMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:provider command.
     *
     * @param  string  $alias
     */
    protected function registerProviderMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ProviderMakeCommand($app['files']);
        });
    }

    /**
     * Register the queue:clear command.
     *
     * @param  string  $alias
     */
    protected function registerQueueClearCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new QueueClearCommand();
        });
    }

    /**
     * Register the queue:failed command.
     *
     * @param  string  $alias
     */
    protected function registerQueueFailedCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new ListFailedQueueCommand();
        });
    }

    /**
     * Register the queue:flush command.
     *
     * @param  string  $alias
     */
    protected function registerQueueFlushCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new FlushFailedQueueCommand();
        });
    }

    /**
     * Register the queue:forget command.
     *
     * @param  string  $alias
     */
    protected function registerQueueForgetCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new ForgetFailedQueueCommand();
        });
    }

    /**
     * Register the queue:listen command.
     *
     * @param  string  $alias
     */
    protected function registerQueueListenCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ListenQueueCommand($app['queue.listener']);
        });
    }

    /**
     * Register the queue:prune-batches command.
     *
     * @param  string  $alias
     */
    protected function registerQueuePruneBatchesCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new PruneBatchesQueueCommand();
        });
    }

    /**
     * Register the queue:restart command.
     *
     * @param  string  $alias
     */
    protected function registerQueueRestartCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new RestartQueueCommand($app['cache.store']);
        });
    }

    /**
     * Register the queue:retry-batch command.
     *
     * @param  string  $alias
     */
    protected function registerQueueRetryBatchCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new QueueRetryBatchCommand();
        });
    }

    /**
     * Register the queue:retry command.
     *
     * @param  string  $alias
     */
    protected function registerQueueRetryCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new RetryQueueCommand();
        });
    }

    /**
     * Register the queue:work command.
     *
     * @param  string  $alias
     */
    protected function registerQueueWorkCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new WorkQueueCommand($app['queue.worker'], $app['cache.store']);
        });
    }

    /**
     * Register the queue:failed-table command.
     *
     * @param  string  $alias
     */
    protected function registerQueueFailedTableCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new FailedTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the queue:table command.
     *
     * @param  string  $alias
     */
    protected function registerQueueTableCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new TableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the queue:batches-table command.
     *
     * @param  string  $alias
     */
    protected function registerQueueBatchesTableCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new BatchesTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the make:request command.
     *
     * @param  string  $alias
     */
    protected function registerRequestMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new RequestMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:resource command.
     *
     * @param  string  $alias
     */
    protected function registerResourceMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ResourceMakeCommand($app['files']);
        });
    }

    /**
     * Register the route:cache command.
     *
     * @param  string  $alias
     */
    protected function registerRouteCacheCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new RouteCacheCommand($app['files']);
        });
    }

    /**
     * Register the route:clear command.
     *
     * @param  string  $alias
     */
    protected function registerRouteClearCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new RouteClearCommand($app['files']);
        });
    }

    /**
     * Register the route:list command.
     *
     * @param  string  $alias
     */
    protected function registerRouteListCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new RouteListCommand($app['router']);
        });
    }

    /**
     * Register the make:rule command.
     *
     * @param  string  $alias
     */
    protected function registerRuleMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new RuleMakeCommand($app['files']);
        });
    }

    /**
     * Register the salts:generate command.
     *
     * @param  string  $alias
     */
    protected function registerSaltsGenerateCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new SaltsGenerateCommand();
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
     * Register the schedule:list command.
     */
    protected function registerScheduleListCommand()
    {
        $this->app->singleton(ScheduleListCommand::class);
    }

    /**
     * Register the schedule:run command.
     */
    protected function registerScheduleRunCommand()
    {
        $this->app->singleton(ScheduleRunCommand::class);
    }

    /**
     * Register the schedule:test command.
     */
    protected function registerScheduleTestCommand()
    {
        $this->app->singleton(ScheduleTestCommand::class);
    }

    /**
     * Register the schedule:work command.
     */
    protected function registerScheduleWorkCommand()
    {
        $this->app->singleton(ScheduleWorkCommand::class);
    }

    /**
     * Register the schema:dump command.
     *
     * @param  string  $alias
     */
    protected function registerSchemaDumpCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new DumpCommand();
        });
    }

    /**
     * Register the db:seed command.
     *
     * @param  string  $alias
     */
    protected function registerSeedCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new SeedCommand($app['db']);
        });
    }

    /**
     * Register the make:seeder command.
     *
     * @param  string  $alias
     */
    protected function registerSeederMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new SeederMakeCommand($app['files']);
        });
    }

    /**
     * Register the serve command.
     *
     * @param  string  $alias
     */
    protected function registerServeCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new ServeCommand();
        });
    }

    /**
     * Register the session:table command.
     *
     * @param  string  $alias
     */
    protected function registerSessionTableCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new SessionTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the storage:link command.
     *
     * @param  string  $alias
     */
    protected function registerStorageLinkCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new StorageLinkCommand();
        });
    }

    /**
     * Register the stub:publish command.
     *
     * @param  string  $alias
     */
    protected function registerStubPublishCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new StubPublishCommand();
        });
    }

    /**
     * Register the make:test command.
     *
     * @param  string  $alias
     */
    protected function registerTestMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new TestMakeCommand($app['files']);
        });
    }

    /**
     * Register the theme:install command.
     *
     * @param  string  $alias
     */
    protected function registerThemeInstallCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ThemeInstallCommand($app['files'], new \ZipArchive());
        });
    }

    /**
     * Register the up command.
     *
     * @param  string  $alias
     */
    public function registerUpCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new UpCommand();
        });
    }

    /**
     * Register the vendor:publish command.
     *
     * @param  string  $alias
     */
    protected function registerVendorPublishCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new VendorPublishCommand($app['files']);
        });
    }

    /**
     * Register the view:cache command.
     *
     * @param  string  $alias
     */
    protected function registerViewCacheCommand($alias)
    {
        $this->app->singleton($alias, function () {
            return new ViewCacheCommand();
        });
    }

    /**
     * Register the view:clear command.
     *
     * @param  string  $alias
     */
    protected function registerViewClearCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
            return new ViewClearCommand($app['files']);
        });
    }

    /**
     * Register the make:widget command.
     *
     * @param  string  $alias
     */
    protected function registerWidgetMakeCommand($alias)
    {
        $this->app->singleton($alias, function ($app) {
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
