<?php

namespace Themosis\Foundation\Http;

use Illuminate\Foundation\Application;

class HttpHandler
{
    public static function front()
    {
        $application = Application::getInstance();
        $request = $application->make('request');

        /** @var Kernel $kernel */
        $kernel = $application->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request);

        $response->send();

        $kernel->terminate($request, $response);
    }
}