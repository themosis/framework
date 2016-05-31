<?php

namespace Themosis\Http;

use Themosis\Foundation\ServiceProvider;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

class HttpServiceProvider extends ServiceProvider
{
    protected $provides = [
        'response',
        'request',
        'emitter'
    ];

    public function register()
    {
        $this->getContainer()->share('response', Response::class);
        $this->getContainer()->share('request', function () {
            return ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        });
        $this->getContainer()->share('emitter', SapiEmitter::class);
    }
}