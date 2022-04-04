<?php

namespace Themosis\Hook\Support;

use Closure;

final class ArgumentCountCalculator
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Calculate the number of arguments of the callback by reflection.
     * @return int
     */
    public function calculate(): int
    {
        if (HookHelper::isHookable($this->callback)) {
            return $this->countForHookable($this->callback);
        }

        if (! is_callable($this->callback)) {
            return 1;
        }

        $callback = Closure::fromCallable($this->callback);

        if (! is_callable($callback)) {
            return 1;
        }

        $reflection = new \ReflectionFunction($callback);

        if ($reflection->getNumberOfParameters() < 1) {
            return 1;
        }

        return (int) $reflection->getNumberOfParameters();
    }

    /**
     * Calculate the number of arguments of the hookable class by reflection.
     * @return int
     */
    private function countForHookable($class): int
    {
        $reflection = new \ReflectionClass($class);

        if (! $reflection->getConstructor()) {
            return 1;
        }

        $number = $reflection->getConstructor()->getNumberOfParameters();

        return $number > 1 ? $number : 1;
    }
}