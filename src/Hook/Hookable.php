<?php

namespace Themosis\Hook;

use Illuminate\Contracts\Foundation\Application;

class Hookable
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string|array
     */
    public $hook;

    /**
     * @var int
     */
    public $priority = 10;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
