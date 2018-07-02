<?php

namespace Themosis\Core;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class HooksRepository
{
    /**
     * @var ApplicationContract
     */
    protected $app;

    public function __construct(ApplicationContract $application)
    {
        $this->app = $application;
    }

    /**
     * Load a list of registered hookable instances.
     *
     * @param array $hooks The list of hookable instances.
     */
    public function load(array $hooks)
    {
        if (empty($hooks)) {
            return;
        }

        foreach ($hooks as $hook) {
            $this->app->registerHook($hook);
        }
    }
}
