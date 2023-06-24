<?php

namespace Themosis\Core\Bus;

class PendingClosureDispatch extends PendingDispatch
{
    /**
     * Add a callback to be executed if the job fails.
     *
     *
     * @return $this
     */
    public function catch(\Closure $callback)
    {
        $this->job->onFailure($callback);

        return $this;
    }
}
