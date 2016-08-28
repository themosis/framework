<?php

namespace Themosis\Route;

class WordPressRouter
{
	/**
	 * Register a new GET route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string|null  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function get($uri, $action = null)
	{
		$this->beforeRouteAddition();
		$route = app('router')->get($uri, $action);
		$this->afterRouteAddition();
		return $route;
	}

	/**
	 * Register a new POST route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string|null  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function post($uri, $action = null)
	{
		$this->beforeRouteAddition();
		$route = app('router')->post($uri, $action);
		$this->afterRouteAddition();
		return $route;
	}

	/**
	 * Register a new PUT route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string|null  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function put($uri, $action = null)
	{
		$this->beforeRouteAddition();
		$route = app('router')->put($uri, $action);
		$this->afterRouteAddition();
		return $route;
	}

	/**
	 * Register a new PATCH route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string|null  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function patch($uri, $action = null)
	{
		$this->beforeRouteAddition();
		$route = app('router')->patch($uri, $action);
		$this->afterRouteAddition();
		return $route;
	}

	/**
	 * Register a new DELETE route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string|null  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function delete($uri, $action = null)
	{
		$this->beforeRouteAddition();
		$route = app('router')->delete($uri, $action);
		$this->afterRouteAddition();
		return $route;
	}

	/**
	 * Register a new OPTIONS route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string|null  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function options($uri, $action = null)
	{
		$this->beforeRouteAddition();
		$route = app('router')->options($uri, $action);
		$this->afterRouteAddition();
		return $route;
	}

	/**
	 * Register a new route responding to all verbs.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string|null  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function any($uri, $action = null)
	{
		$this->beforeRouteAddition();
		$route = app('router')->any($uri, $action);
		$this->afterRouteAddition();
		return $route;
	}

	/**
	 * Register a new route with the given verbs.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  \Closure|array|string|null  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function match($methods, $uri, $action = null)
	{
		$this->beforeRouteAddition();
		$route = app('router')->match($methods, $uri, $action);
		$this->afterRouteAddition();
		return $route;
	}

	protected function beforeRouteAddition()
	{
		app('router')->setCreateAsWordPressRoute(true);
	}

	protected function afterRouteAddition()
	{
		app('router')->setCreateAsWordPressRoute(false);
	}
}