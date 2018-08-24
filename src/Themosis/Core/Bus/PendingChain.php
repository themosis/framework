<?php

namespace Themosis\Core\Bus;

class PendingChain
{
    /**
     * The class name of the job being dispatched.
     *
     * @var string
     */
    public $class;

    /**
     * The jobs to be chained.
     *
     * @var array
     */
    public $chain;

    /**
     * PendingChain constructor.
     *
     * @param string $class
     * @param array  $chain
     */
    public function __construct($class, $chain)
    {
        $this->class = $class;
        $this->chain = $chain;
    }

    /**
     * Dispatch the job with the given arguments.
     *
     * @return PendingDispatch
     */
    public function dispatch()
    {
        return (new PendingDispatch(new $this->class(...func_get_args())))
            ->chain($this->chain);
    }
}
