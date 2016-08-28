<?php

namespace Themosis\Route;

use Themosis\Route\Matching\ConditionMatching;
use Illuminate\Http\Request;

class WordPressRoute extends Route
{
	/**
	 * The WordPress template condition.
	 *
	 * @var string
	 */
	protected $condition;

	/**
	 * WordPress conditional tags.
	 *
	 * @var array
	 */
	protected $conditions = [
		'404' => 'is_404',
		'archive' => 'is_archive',
		'attachment' => 'is_attachment',
		'author' => 'is_author',
		'category' => 'is_category',
		'date' => 'is_date',
		'day' => 'is_day',
		'front' => 'is_front_page',
		'home' => 'is_home',
		'month' => 'is_month',
		'page' => 'is_page',
		'paged' => 'is_paged',
		'postTypeArchive' => 'is_post_type_archive',
		'search' => 'is_search',
		'subpage' => 'themosis_is_subpage',
		'single' => 'is_single',
		'sticky' => 'is_sticky',
		'singular' => 'is_singular',
		'tag' => 'is_tag',
		'tax' => 'is_tax',
		'template' => 'is_page_template',
		'time' => 'is_time',
		'year' => 'is_year',
	];

	/**
	 * Build a Route instance.
	 *
	 * @param array|string $methods
	 * @param string       $uri
	 * @param mixed        $action
	 */
	public function __construct($methods, $uri, $action)
	{
		$this->condition = $this->parseCondition($uri);

		parent::__construct($methods, $uri, $action);
	}

	/**
	 * Parse the route action into a standard array.
	 *
	 * @param \Closure|array $action
	 *
	 * @return array
	 */
	protected function parseAction($action)
	{
		$action = parent::parseAction($action);

		if (!isset($action['conditional_params'])) {
			// The first element passed in the action is used
			// for the WordPress conditional function parameters.
			$param = array_first($action, function ($value, $key) {
				return is_string($value) || is_array($value);
			});

			$action['conditional_params'] = $this->parseConditionalParam($param, $action);
		}

		return $action;
	}

	/**
	 * Parse the action condition parameter value. This is the parameter
	 * given to WordPress conditional functions later.
	 *
	 * @param string|array $param  The condition param value.
	 * @param array        $action The route action params.
	 *
	 * @return mixed
	 */
	protected function parseConditionalParam($param, $action)
	{
		if (is_string($param)) {
			return (false !== strrpos($param, '@')) ? null : $action[0];
		}

		return $param;
	}

	/**
	 * Return the real WordPress conditional tag.
	 *
	 * @param string $uri
	 *
	 * @return string
	 *
	 * @throws \NonExistingRouteConditionException
	 */
	protected function parseCondition($uri)
	{
		// Retrieve all defined WordPress conditions.
		$conditions = $this->getConditions();

		if (isset($conditions[$uri])) {
			return $conditions[$uri];
		}

		throw new \NonExistingRouteConditionException;
	}

	/**
	 * Retrieve the list of registered WordPress conditions.
	 *
	 * @return array
	 */
	protected function getConditions()
	{
		return $this->conditions;
	}

	/**
	 * Allow developers to add route WordPress conditions.
	 *
	 * @param array|string $conditions
	 */
	public function addConditions(array $conditions)
	{
		$this->conditions = $this->conditions + $conditions;
	}

	/**
	 * Get the key / value list of parameters for the route.
	 *
	 * @return array
	 */
	public function parameters()
	{
		global $post, $wp_query;

		// Pass WordPress globals to closures or controller methods as parameters.
		$parameters = array_merge($this->parameters, ['post' => $post, 'query' => $wp_query]);

		// When no posts, $post is null.
		// When is null, set the parameter value of $post to false.
		// This avoid missing arguments in methods for routes or controllers.
		if (is_null($parameters['post'])) {
			$parameters['post'] = false;
		}

		$this->parameters = $parameters;

		return $parameters;
	}

	/**
	 * Determine if the route matches given request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param bool                     $includingMethod
	 *
	 * @return bool
	 */
	public function matches(Request $request, $includingMethod = true)
	{
		// Loop trough every validator and if the route passes, return true else false.
		foreach ($this->getWpValidators() as $validator) {
			return $validator->matches($this, $request);
		}

		return false;
	}

	/**
	 * Get matching validators.
	 *
	 * @return array
	 */
	public function getWpValidators()
	{
		// To match the route, we will use a chain of responsibility pattern with the
		// validator implementations. We will spin through each one making sure it
		// passes and then we will know if the route as a whole matches request.
		return [new ConditionMatching()];
	}

	/**
	 * Get the WordPress condition.
	 *
	 * @return string
	 */
	public function condition()
	{
		return $this->condition;
	}

	/**
	 * Return the 'params' value.
	 *
	 * @return array
	 */
	public function conditionalParameters()
	{
		return isset($this->action['conditional_params']) ? (array) $this->action['conditional_params'] : [];
	}
}