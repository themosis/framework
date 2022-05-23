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
     */
    protected array $conditions = [];

    /**
     * A WordPress condition.
     */
    protected ?string $condition;

    /**
     * Condition parameters.
     */
    protected array $conditionParams = [];

    /**
     * List of WordPress route validators.
     */
    public static array $wordpressValidators;

    /**
     * Determine if the route matches given request.
     *
     * @param Request $request
     * @param bool    $includingMethod
     *
     * @return bool
     */
    public function matches(Request $request, $includingMethod = true): bool
    {
        $this->compileRoute();

        /**
         * Let's first check if we're working with a WordPress route.
         * If not a WordPress route, we go through each Illuminate
         * validators to match the request.
         */
        if ($this->hasWordPressCondition()) {
            foreach (self::getWordPressValidators() as $validator) {
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
     * Check if route has a WordPress condition.
     */
    public function hasWordPressCondition(): bool
    {
        return ! empty($this->condition);
    }

    public function setWordPressConditions(array $conditions = []): self
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
    protected function parseCondition(string $condition): ?string
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
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * Return registered conditions.
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * Return the route condition parameters.
     */
    public function getConditionParameters(): array
    {
        return $this->conditionParams;
    }

    /**
     * Get the WordPress route validators for the instance.
     */
    public static function getWordPressValidators(): array
    {
        if (isset(static::$wordpressValidators)) {
            return static::$wordpressValidators;
        }

        return static::$wordpressValidators = [
            new ConditionValidator(),
        ];
    }
}
