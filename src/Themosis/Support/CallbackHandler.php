<?php

namespace Themosis\Support;

use Illuminate\Contracts\Container\Container;

trait CallbackHandler
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Set the instance container.
     *
     * @param Container $container
     *
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Handle the callback to execute.
     *
     * @param string|array|callable $callback
     *
     * @return mixed|string
     */
    protected function handleCallback($callback)
    {
        $response = null;

        // Check if $callback is a closure.
        if ($callback instanceof \Closure || is_array($callback)) {
            $response = call_user_func($callback);
        } elseif (is_string($callback)) {
            if (is_callable($callback)) {
                // Used as a classic callback function.
                $response = call_user_func($callback);
            } else {
                // We use a "ClassName@method" syntax.
                // Let's get a class instance and call its method.
                $callbackArray = $this->handleClassCallback($callback);
                $response = call_user_func($callbackArray);
            }
        }

        return $response;
    }

    /**
     * Handle a class callback using "ClassName@method" syntax
     *
     * @param string $callback
     *
     * @return array
     */
    protected function handleClassCallback(string $callback): array
    {
        list($class, $method) = $this->parseCallback($callback);
        $instance = $this->container->make($class);

        return [$instance, $method];
    }

    /**
     * Return an array with the class name and its method.
     *
     * @param string $callback
     *
     * @return array
     */
    protected function parseCallback(string $callback): array
    {
        if (str_contains($callback, '@')) {
            return explode('@', $callback);
        }

        // If no method is defined, use the "index" name by default.
        return [$callback, 'index'];
    }
}
