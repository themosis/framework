<?php

namespace Themosis\Route;

use Illuminate\Routing\Router as IlluminateRouter;

class Router extends IlluminateRouter
{
    /**
     * WordPress conditions.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * Create a new Route object.
     *
     * @param array|string $methods
     * @param string       $uri
     * @param mixed        $action
     *
     * @return \Illuminate\Routing\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        // WordPress condition could have been already applied.
        // We only try one more time to fetch them if no conditions
        // are registered. This avoids the overwrite of any pre-existing rules.
        if (empty($this->conditions)) {
            $this->setConditions();
        }

        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container)
            ->setConditions($this->conditions);
    }

    /**
     * Setup WordPress conditions.
     *
     * @param array $conditions
     */
    public function setConditions(array $conditions = [])
    {
        $config = $this->container->has('config') ? $this->container->make('config') : null;

        if (! is_null($config)) {
            $this->conditions = array_merge(
                $config->get('app.conditions', []),
                $conditions
            );
        } else {
            $this->conditions = $conditions;
        }
    }

    /**
     * Add WordPress default parameters if WordPress route.
     *
     * @param \Themosis\Route\Route $route
     *
     * @return \Themosis\Route\Route
     */
    public function addWordPressBindings($route)
    {
        global $post, $wp_query;

        $parameters = [
            'post' => $post,
            'query' => $wp_query
        ];

        foreach ($parameters as $key => $value) {
            $route->setParameter($key, $value);
        }

        return $route;
    }
}
