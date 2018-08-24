<?php

namespace Themosis\Core\Bus;

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
     * Set the jobs that should run if this job is successful.
     *
     * @param array $chain
     *
     * @return PendingChain
     */
    public static function withChain($chain)
    {
        return new PendingChain(get_called_class(), $chain);
    }
}
