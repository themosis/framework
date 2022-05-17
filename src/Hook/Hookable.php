<?php

namespace Themosis\Hook;

use Illuminate\Contracts\Foundation\Application;

class Hookable
{
    protected Application $app;

    public string|array $hook;

    public int $priority = 10;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
