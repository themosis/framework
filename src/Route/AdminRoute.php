<?php

namespace Themosis\Route;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router as IlluminateRouter;

class AdminRoute
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var IlluminateRouter
     */
    private $router;

    public function __construct(Request $request, IlluminateRouter $router)
    {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * Return the catch-all WordPress administration route.
     *
     * @return \Illuminate\Routing\Route
     */
    public function get()
    {
        $route = $this->router->any('cms/wp-admin/{any?}', function () {
            return new Response();
        });

        $route->middleware('admin');
        $route->bind($this->request);

        return $route;
    }
}
