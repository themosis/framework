<?php

namespace Themosis\Core\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\InteractsWithTime;
use Themosis\Core\Application;

class VerifyCsrfToken
{
    use InteractsWithTime;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Encrypter
     */
    protected $encrypter;

    public function __construct(Application $application, Encrypter $encrypter)
    {
        $this->app = $application;
        $this->encrypter = $encrypter;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     */
    public function handle($request, \Closure $next)
    {
    }
}
