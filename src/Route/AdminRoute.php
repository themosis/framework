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
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return \Illuminate\Routing\Route
     */
    public function get()
    {
        $wordpressUri = trim(config('app.wp.dir', 'cms'), '\/');
        $route = $this->router->any($wordpressUri.'/wp-admin/{any?}', function () {
            return new Response();
        });

        $route->middleware('admin');
        $route->bind($this->request);

        return $route;
    }
}
