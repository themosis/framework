<?php

namespace Themosis\Route;

use League\Container\ReflectionContainer;
use League\Route\RouteCollection;
use League\Route\Strategy\ParamStrategy;
use Themosis\Foundation\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $provides = [
        'router'
    ];

    public function register()
    {
        //$this->getContainer()->share('router', 'Themosis\Route\Router')->withArgument($this->getContainer());
        $route = new RouteCollection($this->getContainer());
        //$route->setStrategy(new ParamStrategy());

        $this->getContainer()->add('router', $route);
    }
}
