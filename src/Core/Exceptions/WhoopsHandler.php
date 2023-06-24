<?php

namespace Themosis\Core\Exceptions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Whoops\Handler\PrettyPageHandler;

class WhoopsHandler
{
    /**
     * Create a new Whoops handler for debug mode.
     *
     * @return PrettyPageHandler
     */
    public function forDebug()
    {
        return tap(new PrettyPageHandler(), function ($handler) {
            $handler->handleUnconditionally(true);

            $this->registerApplicationPaths($handler)
                ->registerBlackList($handler)
                ->registerEditor($handler);
        });
    }

    /**
     * Register the application paths with the handler.
     *
     * @param  PrettyPageHandler  $handler
     * @return $this
     */
    protected function registerApplicationPaths($handler)
    {
        $handler->setApplicationPaths(
            array_flip($this->directoriesExceptVendor()),
        );

        return $this;
    }

    /**
     * Get the application paths except for the "vendor" directory.
     *
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function directoriesExceptVendor()
    {
        return Arr::except(
            array_flip((new Filesystem())->directories(base_path())),
            [base_path('vendor')],
        );
    }

    /**
     * Register the blacklist with the handler.
     *
     * @param  \Whoops\Handler\PrettyPageHandler  $handler
     * @return $this
     */
    protected function registerBlacklist($handler)
    {
        foreach (config('app.debug_blacklist', config('app.debug_hide', [])) as $key => $secrets) {
            foreach ($secrets as $secret) {
                $handler->blacklist($key, $secret);
            }
        }

        return $this;
    }

    /**
     * Register the editor with the handler.
     *
     * @param  \Whoops\Handler\PrettyPageHandler  $handler
     * @return $this
     */
    protected function registerEditor($handler)
    {
        if (config('app.editor', false)) {
            $handler->setEditor(config('app.editor'));
        }

        return $this;
    }
}
