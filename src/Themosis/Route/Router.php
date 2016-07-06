<?php

namespace Themosis\Route;

use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router as IlluminateRouter;
use Themosis\Foundation\Application;

class Router extends IlluminateRouter
{
    /**
     * Build a Router instance.
     *
     * @param \Illuminate\Events\Dispatcher    $events
     * @param \Themosis\Foundation\Application $container
     */
    public function __construct(Dispatcher $events, Application $container)
    {
        parent::__construct($events, $container);
        $this->routes = new RouteCollection();
    }

    /**
     * Create a new Themosis Route object.
     *
     * @param  array|string $methods
     * @param  string       $uri
     * @param  mixed        $action
     * @return \Illuminate\Routing\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    /**
     * Find the route matching a given request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Themosis\Route\Route
     */
    protected function findRoute($request)
    {
        $route = parent::findRoute($request);

        // If the current route is a WordPress route
        if ($route instanceof Route && !$route->condition()) {

            global $wp;
            //Check if the route is not WordPress route and there is no rewrite rule created for the given route
            if ($wp->matched_rule != $route->getRewriteRuleRegex()) {

                $text = 'There were routes non-Wordpress routes created but the needed rewrite rules are not available. Please refresh the permalinks on the settings page to fix this problem.
                    By default WordPress can not handle static routes like their working in Themosis using Laravel it\'s router. Because of this we create a rewrite rule for every non-WordPress route.
                    The only good way to refresh the rewrite rules is to do it manually.</br></br>
                    After every route definition change just refresh your permalinks to avoid unpredicted errors.';
                $title = 'WordPress - Missing Rewrite Rules';

                /*
                 * Add a notice in the wp-admin.
                 */
                add_action('admin_notices', function () use ($text) {
                    printf('<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $text);
                });

                /*
                 * Add a notice in the front-end.
                 */
                wp_die($text, $title);
            }

            global $wp_query;
            // Set the is_home to false inside the wp_query
            $wp_query->is_home = false;
        }

        return $route;
    }
}
