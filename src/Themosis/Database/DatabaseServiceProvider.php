<?php

namespace Themosis\Database;

use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Themosis\Foundation\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot(){

		Model::setConnectionResolver($this->app['db']);

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		Model::clearBootedModels();

		// The connection factory is used to create the actual connection instances on
		// the database. We will inject the factory into the manager so that it may
		// make the connections while they are actually needed and not of before.
		$this->app->singleton('db.factory', function ($app) {
			return new ConnectionFactory($app);
		});

		// The database manager is used to resolve various connections, since multiple
		// connections might be managed. It also implements the connection resolver
		// interface which may be used by other components requiring connections.
		$this->app->singleton('db', function ($app) {
			return new DatabaseManager($app, $app['db.factory']);
		});

		$this->app->bind('db.connection', function ($app) {
			return $app['db']->connection();
		});
	}


}
