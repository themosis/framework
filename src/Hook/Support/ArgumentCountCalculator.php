<?php

namespace Themosis\Hook\Support;

use Closure;
use ReflectionClass;
use ReflectionFunction;

final class ArgumentCountCalculator
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @throws \ReflectionException
     */
    public function calculate()
    {
        if (HookHelper::isHookable($this->callback)) {
            return $this->countForHookable($this->callback);
        }

        if (!is_callable($this->callback)) {
            return 1;
        }

        $callback = Closure::fromCallable($this->callback);

        if (!is_callable($callback)) {
            return 1;
        }

        $reflection = new ReflectionFunction($callback);

        if ($reflection->getNumberOfParameters() < 1) {
            return 1;
        }

        return (int)$reflection->getNumberOfParameters();
    }

    private function countForHookable($class)
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->getConstructor()) {
            return 1;
        }

        $number = $reflection->getConstructor()->getNumberOfParameters();

        return max($number, 1);
    }

    public static function make($callback)
    {
        return new self($callback);
    }
}