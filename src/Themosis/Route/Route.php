<?php

namespace Themosis\Route;

use Illuminate\Http\Request;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Arr;

class Route extends IlluminateRoute
{
    /**
     * WordPress conditions rules.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * A WordPress condition.
     *
     * @var string
     */
    protected $condition = '';

    /**
     * Condition parameters.
     *
     * @var array
     */
    protected $conditionParams = [];

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
        $this->compileRoute();

        return true;
    }

    /**
     * Set route WordPress conditions rules.
     *
     * @param array $conditions
     *
     * @return \Themosis\Route\Route
     */
    public function setConditions(array $conditions = [])
    {
        $this->conditions = $conditions;

        $this->condition = $this->parseCondition($this->uri());
        $this->conditionParams = $this->parseConditionParams($this->getAction());

        return $this;
    }

    /**
     * Parse the route condition based on global list of conditions.
     * Return the WordPress conditional function.
     *
     * @param string $condition
     *
     * @return string
     */
    protected function parseCondition(string $condition): string
    {
        return $this->getConditions()[$condition] ?? '';
    }

    /**
     * Parse route action and get any WordPress conditional parameters
     * if any defined.
     *
     * @param array $action
     *
     * @return array
     */
    protected function parseConditionParams(array $action): array
    {
        if (empty($this->condition)) {
            return [];
        }

        $params = Arr::first($action, function ($value, $key) {
            return is_numeric($key);
        });

        return [$params];
    }

    /**
     * Return registered conditions.
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
