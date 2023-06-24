<?php

namespace Themosis\Core\Bus;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Fluent;

trait Dispatchable
{
    /**
     * Dispatch the job with the given arguments.
     *
     * @return PendingDispatch
     */
    public static function dispatch()
    {
        return new PendingDispatch(new static(...func_get_args()));
    }

    /**
     * Dispatch the job with the given arguments if the given truth test passes.
     *
     * @param  bool  $boolean
     * @param  mixed  ...$arguments
     * @return Fluent|PendingDispatch
     */
    public static function dispatchIf($boolean, ...$arguments)
    {
        return $boolean
            ? new PendingDispatch(new static(...$arguments))
            : new Fluent();
    }

    /**
     * Dispatch the job with the given arguments unless the given truth test passes.
     *
     * @param  mixed  ...$arguments
     * @return Fluent|PendingDispatch
     */
    public static function dispatchUnless($boolean, ...$arguments)
    {
        return ! $boolean
            ? new PendingDispatch(new static(...$arguments))
            : new Fluent();
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queuable jobs will be dispatched to the "sync" queue.
     *
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function dispatchSync()
    {
        return app(Dispatcher::class)->dispatchSync(new static(...func_get_args()));
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function dispatchNow()
    {
        return app(Dispatcher::class)->dispatchNow(new static(...func_get_args()));
    }

    /**
     * Dispatch a command to its appropriate handle after the current process.
     *
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function dispatchAfterResponse()
    {
        return app(Dispatcher::class)->dispatchAfterResponse(new static(...func_get_args()));
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param  array  $chain
     * @return PendingChain
     */
    public static function withChain($chain)
    {
        return new PendingChain(static::class, $chain);
    }
}
