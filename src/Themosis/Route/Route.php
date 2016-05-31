<?php

namespace Themosis\Route;

use Illuminate\Http\Request;
use Illuminate\Routing\Matching\MethodValidator;
use Illuminate\Routing\Matching\SchemeValidator;
use Illuminate\Routing\Route as IlluminateRoute;
use Themosis\Route\Matching\ConditionValidator;
use Themosis\Route\Matching\TemplateValidator;

class Route extends IlluminateRoute {

    /**
     * WordPress conditional tags.
     *
     * @var array
     */
    protected $conditions = [
        'archive'         => 'is_archive',
        'attachment'      => 'is_attachment',
        'author'          => 'is_author',
        'category'        => 'is_category',
        'date'            => 'is_date',
        'day'             => 'is_day',
        'front'           => 'is_front_page',
        'home'            => 'is_home',
        'month'           => 'is_month',
        'page'            => 'is_page',
        'paged'           => 'is_paged',
        'postTypeArchive' => 'is_post_type_archive',
        'search'          => 'is_search',
        'subpage'         => 'themosis_is_subpage',
        'single'          => 'is_single',
        'sticky'          => 'is_sticky',
        'singular'        => 'is_singular',
        'tag'             => 'is_tag',
        'tax'             => 'is_tax',
        'template'        => 'themosis_is_template',
        'time'            => 'is_time',
        'year'            => 'is_year'
    ];

    /**
     * The WordPress template condition.
     *
     * @see https://codex.wordpress.org/Conditional_Tags
     *
     * @var string
     */
    protected $condition;

    public function __construct(array $methods, $uri, $action)
    {
        parent::__construct($methods, $uri, $action);

        $this->parameters = [];
        $this->condition = $this->parseCondition($uri);

        $this->createRewriteRule();
    }

    protected function parseAction($action)
    {
        $action = parent::parseAction($action);

        if (!isset($action['conditional_params'])) {
            // The first element passed in the action is used
            // for the WordPress conditional function parameters.
            $param = array_first($action, function ($key, $value) {
                return is_string($value) || is_array($value);
            });

            $action['conditional_params'] = $this->parseConditionalParam($param, $action);
        }

        return $action;
    }

    /**
     * Parse the action condition parameter value. This is the parameter
     * given to WordPress conditional functions later.
     *
     * @param string|array $param The condition param value.
     * @param array $action The route action params.
     * @return mixed
     */
    protected function parseConditionalParam($param, $action)
    {
        if (is_string($param)) {
            return (false !== strrpos($param, '@')) ? null : $action[0];
        }

        return $param;
    }

    /**
     * Return the real WordPress conditional tag by using the uri tag
     *
     * @param string $uri
     * @return string
     */
    protected function parseCondition($uri)
    {
        $conditions = $this->getConditions();

        if (isset($conditions[$uri])) {
            return $conditions[$uri];
        }

        return null;
    }

    /**
     * Get all the route default and custom route conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        // Allow developers to define non-core conditions by providing a key/value property.
        $conditions = apply_filters('themosisRouteConditions', []);
        return $this->conditions + $conditions;
    }

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
     * First the method will check if the route is a conditional route (a WordPress route).
     * If not, it will check if the route matches using laravel's own route functionality.
     * If not, it will check if the given route url matches "404"
     *
     * @param  \Illuminate\Http\Request $request
     * @param  bool $includingMethod
     * @return bool
     */
    public function matches(Request $request, $includingMethod = true)
    {
        // If this route uses a WordPress conditional tag
        if ($this->condition()) {
            // Loop trough every validator and if the route passes every validator has passed return true
            foreach ($this->getWpValidators() as $validator) {
                if (!$includingMethod && $validator instanceof MethodValidator) {
                    continue;
                }

                if (!$validator->matches($this, $request)) {
                    return false;
                };
            }

            return true;

        }

        // If no WordPress condition is found, use the normal way of getting a route
        $matches = parent::matches($request, $includingMethod);

        // If we can not find a route using the normal laravel router check if the route which is being checked has the uri "404". If so we return this route as the valid one.
        return !$matches && $this->getUri() === '404' ? true : $matches;
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
     * Return the 'params' value.
     *
     * @return array
     */
    public function conditionalParameters()
    {
        return isset($this->action['conditional_params']) ? (array)$this->action['conditional_params'] : [];
    }

    /**
     * Compile the route into a Symfony CompiledRoute instance.
     *
     * @return void
     */
    public function compileRoute()
    {
        parent::compileRoute();
    }

    /**
     * Get the route WordPress validators for the instance.
     *
     * @return array
     */
    public static function getWpValidators()
    {
        // To match the route, we will use a chain of responsibility pattern with the
        // validator implementations. We will spin through each one making sure it
        // passes and then we will know if the route as a whole matches request.
        return [new ConditionValidator, new TemplateValidator, new SchemeValidator];
    }

    /**
     * Create a WordPress rewrite rule for the route if it the route is not using a WordPress conditional tag.
     * By registering a rewrite rule using the route's regex we force WordPress not to change the url to one Wordpress knows.
     */
    public function createRewriteRule()
    {
        if (!$this->condition()) {
            // Compile the route to get a Symfony compiled route
            $this->compileRoute();
            // Get the regex of the compiled route
            $routeRegex = $this->getCompiled()->getRegex();
            // Remove the first part (#^/) of the regex because WordPress adds this already by itself
            $routeRegex = preg_replace('/^\#\^\//', '^', $routeRegex);
            // Remove the last part (#s$) of the regex because WordPress adds this already by itself
            $routeRegex = preg_replace('/\#[s]$/', '', $routeRegex);

            // Add the rewrite rule to the top
            add_action('init', function () use ($routeRegex) {
                add_rewrite_rule($routeRegex, 'index.php', 'top');
            });

        }
    }
}