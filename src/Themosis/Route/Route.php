<?php

namespace Themosis\Route;

use Illuminate\Http\Request;
use Illuminate\Routing\Matching\MethodValidator;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Arr;
use Themosis\Route\Matching\ConditionValidator;

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
     * List of WordPress route validators.
     *
     * @var array
     */
    protected $wordpressValidators;

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

        // Let's first check if we're working with a WordPress route.
        // If not a WordPress route, we go through each Illuminate
        // validators to match the request.
        if ($this->hasCondition()) {
            foreach ($this->getWordPressValidators() as $validator) {
                if (! $validator->matches($this, $request)) {
                    return false;
                }
            }

            return true;
        }

        // The route is not a WordPress one.
        // Let's loop through the Illuminate validators in order
        // to match the request.
        foreach ($this->getValidators() as $validator) {
            if (! $includingMethod && $validator instanceof MethodValidator) {
                continue;
            }

            if (! $validator->matches($this, $request)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if route has a condition available.
     * Meaning this is a WordPress route.
     *
     * @return bool
     */
    public function hasCondition(): bool
    {
        return ! empty($this->condition);
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
        $conditions = $this->getConditions();

        foreach ($conditions as $signature => $conds) {
            $conds = is_array($conds) ? $conds : [$conds];

            if (in_array($condition, $conds, true)) {
                return $signature;
            }
        }

        return '';
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
     * Return the route condition.
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
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

    /**
     * Return the route condition parameters.
     *
     * @return array
     */
    public function getConditionParameters(): array
    {
        return $this->conditionParams;
    }

    /**
     * Get the WordPress route validators for the instance.
     *
     * @return array
     */
    public function getWordPressValidators()
    {
        if (isset($this->wordpressValidators)) {
            return $this->wordpressValidators;
        }

        return $this->wordpressValidators = [
            new ConditionValidator
        ];
    }
}
