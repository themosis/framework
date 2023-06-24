<?php

namespace Themosis\Support;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

trait CallbackHandler
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Set the instance container.
     *
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
     * @param  string|array|callable  $callback
     * @return mixed|string
     */
    protected function handleCallback($callback, array $args = [], bool $asArray = true)
    {
        $response = null;

        $handleResponse = function ($callback, array $args, bool $asArray) {
            return $asArray
                ? call_user_func($callback, $args)
                : call_user_func_array($callback, $args);
        };

        // Check if $callback is a closure.
        if ($callback instanceof \Closure || is_array($callback)) {
            $response = $handleResponse($callback, $args, $asArray);
        } elseif (is_string($callback)) {
            if (false !== strpos($callback, '@') || class_exists($callback)) {
                // We use a "ClassName@method" syntax.
                // Let's get a class instance and call its method.
                $callbackArray = $this->handleClassCallback($callback);
                $response = $handleResponse($callbackArray, $args, $asArray);
            } else {
                // Used as a classic callback function.
                $response = $handleResponse($callback, $args, $asArray);
            }
        }

        return $response;
    }

    /**
     * Handle a class callback using "ClassName@method" syntax
     *
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function handleClassCallback(string $callback): array
    {
        [$class, $method] = $this->parseCallback($callback);
        $instance = $this->container->make($class);

        return [$instance, $method];
    }

    /**
     * Return an array with the class name and its method.
     */
    protected function parseCallback(string $callback): array
    {
        if (Str::contains($callback, '@')) {
            return explode('@', $callback);
        }

        // If no method is defined, use the "index" name by default.
        return [$callback, 'index'];
    }
}
