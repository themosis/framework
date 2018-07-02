<?php

namespace Themosis\Hook;

use Illuminate\Contracts\Foundation\Application;

class Hookable
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
