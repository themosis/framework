<?php

namespace Themosis\Route;

use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Themosis\Route\Matching\ConditionMatching;

class Route extends IlluminateRoute
{
    /**
     * The WordPress template condition.
     *
     * @var string
     */
    protected $condition;

    /**
     * The additional parameters for the WordPress template conditions
     *
     * @var array|void
     */
    protected $conditionalParameters = [];

    /**
     * The prefix used to name the custom route tag.
     *
     * @var string
     */
    protected $rewrite_tag_prefix = 'themosis';

    /**
     * WordPress conditional tags.
     *
     * @var array
     */
    protected $conditions = [
        '404' => 'is_404',
        'archive' => 'is_archive',
        'attachment' => 'is_attachment',
        'author' => 'is_author',
        'category' => 'is_category',
        'date' => 'is_date',
        'day' => 'is_day',
        'front' => 'is_front_page',
        'home' => 'is_home',
        'month' => 'is_month',
        'page' => 'is_page',
        'paged' => 'is_paged',
        'postTypeArchive' => 'is_post_type_archive',
        'search' => 'is_search',
        'subpage' => 'themosis_is_subpage',
        'single' => 'is_single',
        'sticky' => 'is_sticky',
        'singular' => 'is_singular',
        'tag' => 'is_tag',
        'tax' => 'is_tax',
        'template' => 'is_page_template',
        'time' => 'is_time',
        'year' => 'is_year',
    ];

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

        $this->parameters = [];

        /*
         * Parse the WordPress condition together with the parameters
         */
        $this->condition = $this->parseCondition($uri);
        $this->conditionalParameters = $this->parseConditionalParameters($action);

        /*
         * Create a WordPress rewrite rule if the route is a non-WordPress route
         */
        $this->createRewriteRule();
    }

    /**
     * Parses the conditional parameter out of the action parameter. This is the parameter
     * given to WordPress conditional functions later.
     *
     * @param array        $action The action parameter where the conditional parameters are in
     *
     * @return array       An array with the conditional parameters or null
     */
    protected function parseConditionalParameters($action)
    {
        // Retrieve parameters. Accept only string or array.
        // This help filter the $action parameters as it might also be a Closure.
        $parameters = Arr::first($action, function ($value, $key) {
            return is_string($value) || is_array($value);
        });

        if ($this->condition() && !is_null($parameters))
        {
            if (is_string($parameters) && strrpos($parameters, '@') !== false) {
                /**
                 * In case of a controller value statement, return empty array.
                 */
                return [];
            }

            return is_array($parameters) ? $parameters : [$parameters];
        }

        return [];
    }

    /**
     * Return the real WordPress conditional tag.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function parseCondition($uri)
    {
        // Retrieve all defined WordPress conditions.
        $conditions = $this->getConditions();

        if (isset($conditions[$uri])) {
            return $conditions[$uri];
        }

        return null;
    }

    /**
     * Retrieve the list of registered WordPress conditions.
     *
     * @return array
     */
    protected function getConditions()
    {
        return apply_filters('themosisRouteConditions', $this->conditions);
    }

    /**
     * Allow developers to add route WordPress conditions.
     *
     * @param array|string $conditions
     */
    public function addConditions(array $conditions)
    {
        $this->conditions = $this->conditions + $conditions;
    }

    /**
     * Get the key / value list of parameters for the route.
     *
     * @return array
     */
    public function parameters()
    {
        if ($this->condition) {
            global $post, $wp_query;

            // Pass WordPress globals to closures or controller methods as parameters.
            $parameters = array_merge($this->parameters, ['post' => $post, 'query' => $wp_query]);

            // When no posts, $post is null.
            // When is null, set the parameter value of $post to false.
            // This avoid missing arguments in methods for routes or controllers.
            if (is_null($parameters['post'])) {
                $parameters['post'] = false;
            }

            $this->parameters = $parameters;

            return $parameters;
        }

        return parent::parameters();
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
        // If this route uses a WordPress conditional tag
        if ($this->condition()) {
            // Loop trough every validator and if the route passes, return true else false.
            foreach ($this->getWpValidators() as $validator) {
                return $validator->matches($this, $request);
            }

            return false;
        }

        // If no WordPress condition is found, use the normal way of getting a route
        $matches = parent::matches($request, $includingMethod);

        // If we can not find a route using the normal laravel router check if the route which is being checked has the uri "404". If so we return this route as the valid one.
        if (method_exists($this, 'getUri')) {
            return !$matches && $this->getUri() === '404' ? true : $matches;
        }

        return !$matches && $this->uri() === '404' ? true : $matches;
    }

    /**
     * Get matching validators.
     *
     * @return array
     */
    public function getWpValidators()
    {
        // To match the route, we will use a chain of responsibility pattern with the
        // validator implementations. We will spin through each one making sure it
        // passes and then we will know if the route as a whole matches request.
        return [new ConditionMatching()];
    }

    /**
     * Create a WordPress rewrite rule for the route if the route is not using a WordPress conditional tag.
     * By registering a rewrite rule using the route's regex we force WordPress not to change the url to one Wordpress knows.
     *
     */
    public function createRewriteRule()
    {
        if (!$this->condition()) {
            // Compile the route to get a Symfony compiled route
            $this->compileRoute();

            // Retrieve the regex to use of registering the rewrite rule for this route
            $regex = $this->getRewriteRuleRegex();

            // Add the rewrite rule to the top
            add_action('init', function () use ($regex) {
                add_rewrite_tag('%is_'.$this->rewrite_tag_prefix.'_route%', '(\d)');
                add_rewrite_rule($regex, 'index.php?is_'.$this->rewrite_tag_prefix.'_route=1', 'top');
            });
        }
    }

    /**
     * Get the WordPress condition.
     *
     * @return string
     */
    public function condition()
    {
        return $this->condition;
    }

    /**
     * Return the conditional parameters
     *
     * @return array
     */
    public function conditionalParameters()
    {
        return $this->conditionalParameters;
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
        // Remove the modifiers of the regex because WordPress adds this already by itself
        $routeRegex = preg_replace('/\$\#.*$/', '$', $routeRegex);

        return $routeRegex;
    }
}
