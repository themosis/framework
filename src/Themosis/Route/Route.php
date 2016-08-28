<?php

namespace Themosis\Route;

use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Http\Request;

class Route extends IlluminateRoute
{
    /**
     * The prefix used to name the custom route tag.
     *
     * @var string
     */
    protected $rewrite_tag_prefix = 'themosis';

    /**
     * Build a Route instance.
     *
     * @param array|string $methods
     * @param string       $uri
     * @param mixed        $action
     */
    public function __construct($methods, $uri, $action)
    {
        parent::__construct($methods, $uri, $action);

        $this->createRewriteRule();
    }

    /**
     * Determine if the route matches given request.
     *
     * @param \Illuminate\Http\Request $request
     * @param bool                     $includingMethod
     *
     * @return bool
     */
    public function matches(Request $request, $includingMethod = true)
    {
        $matches = parent::matches($request, $includingMethod);

        // If we can not find a route using the normal laravel router check if the route which is being checked has the uri "404". If so we return this route as the valid one.
        return !$matches && $this->getUri() === '404' ? true : $matches;
    }

    /**
     * Create a WordPress rewrite rule for the route.
     * By registering a rewrite rule using the route's regex we force WordPress not to change the url to one Wordpress knows.
     *
     * @return array
     */
    public function createRewriteRule()
    {
	    // Compile the route to get a Symfony compiled route
	    $this->compileRoute();

	    // Retrieve the regex to use of registering the rewrite rule for this route
	    $regex = $this->getRewriteRuleRegex();

	    // Add the rewrite rule to the top
	    add_action('init', function () use ($regex) {
		    add_rewrite_tag('%is_'.$this->rewrite_tag_prefix.'_route%', '(\d)');
		    add_rewrite_rule($regex, 'index.php?is_' . $this->rewrite_tag_prefix . '_route=1', 'top');
	    });
    }

    /**
     * Returns the regex to be registered as a rewrite rule to let WordPress know the existence of this route
     *
     * @return mixed|string
     */
    public function getRewriteRuleRegex()
    {
        // Get the regex of the compiled route
        $routeRegex = $this->getCompiled()->getRegex();
        // Remove the first part (#^/) of the regex because WordPress adds this already by itself
        $routeRegex = preg_replace('/^\#\^\//', '^', $routeRegex);
        // Remove the last part (#s$) of the regex because WordPress adds this already by itself
        $routeRegex = preg_replace('/\#[s]$/', '', $routeRegex);

        return $routeRegex;
    }
}
