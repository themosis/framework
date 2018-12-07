<?php

namespace Themosis\Metabox;

use Themosis\Core\Support\Providers\RouteServiceProvider;
use Themosis\Support\Facades\Route;

class MetaboxRoutingServiceProvider extends RouteServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the metabox API routes.
     */
    public function map()
    {
        Route::middleware('api')
            ->namespace('Themosis\Metabox\Controllers')
            ->prefix('wp-api/themosis/v1')
            ->group(function () {
                Route::apiResource('metabox', 'MetaboxApiController')->only([
                    'show', 'update'
                ]);
            });
    }
}
