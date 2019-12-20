<?php

namespace Themosis\Core\Events;

trait Dispatchable
{
    /**
     * Dispatch the event with the given arguments.
     */
    public static function dispatch()
    {
        return event(new static(...func_get_args()));
    }

    /**
     * Broadcast the event with the given arguments.
     *
     * @return \Illuminate\Broadcasting\PendingBroadcast
     */
    public static function broadcast()
    {
        return broadcast(new static(...func_get_args()));
    }
}
