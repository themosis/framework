<?php
namespace Themosis\Route;

class Route {

    /**
     * The WordPress template condition.
     *
     * @var string
     */
    protected $condition;

    /**
     * HTTP methods
     *
     * @var array
     */
    protected $methods;

    /**
     * Route actions.
     *
     * @var array
     */
    protected $action;

    /**
     * Parameters passed to the route callback or controller action method.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * WordPress conditional tags.
     *
     * @var array
     */
    protected $conditions = [
        '404'			       => 'is_404',
        'archive'		       => 'is_archive',
        'attachment'	       => 'is_attachment',
        'author'		       => 'is_author',
        'category'		       => 'is_category',
        'date'			       => 'is_date',
        'day'			       => 'is_day',
        'front'			       => 'is_front_page',
        'home'			       => 'is_home',
        'month'			       => 'is_month',
        'page'			       => 'is_page',
        'paged'			       => 'is_paged',
        'postTypeArchive'      => 'is_post_type_archive',
        'search'		       => 'is_search',
        'subpage'		       => 'themosis_is_subpage',
        'single'		       => 'is_single',
        'sticky'		       => 'is_sticky',
        'singular'		       => 'is_singular',
        'tag'			       => 'is_tag',
        'tax'			       => 'is_tax',
        'template'             => 'themosis_is_template',
        'time'			       => 'is_time',
        'year'			       => 'is_year'
    ];

    /**
     * Build a Route instance.
     *
     * @param array|string $methods
     * @param string $condition
     * @param mixed $action
     */
    public function __construct($methods, $condition, $action)
    {
        $this->methods = (array) $methods;
        $this->condition = $this->parseCondition($condition);
        $this->action = $this->parseAction($action);
    }

    /**
     * Parse the route action into a standard array.
     *
     * @param \Closure|array $action
     * @return array
     */
    protected function parseAction($action)
    {
        // If the action is already a Closure instance, we will just set that instance
        // as the "uses" property, because there is nothing else we need to do when
        // it is available. Otherwise we will need to find it in the action list.
        if (is_callable($action))
        {
            return ['uses' => $action];
        }
        elseif (!isset($action['uses']))
        {
            // If no "uses" property has been set, we will dig through the array to find a
            // Closure instance within this list. We will set the first Closure we come
            // across into the "uses" property that will get fired off by this route.
            $action['uses'] = $this->findClosure($action);
        }

        if (!isset($action['params']))
        {
            // The first element passed in the action is used
            // for the WordPress conditional function parameters.
            $param = array_first($action, function($key, $value)
            {
                return is_string($value) || is_array($value);
            });

            $action['params'] = $this->parseParam($param, $action);
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
    protected function parseParam($param, $action)
    {
        if (is_string($param))
        {
            return (false !== strrpos($param, '@')) ? null : $action[0];
        }

        return $param;
    }

    /**
     * Return the real WordPress conditional tag.
     *
     * @param string $condition
     * @return string
     * @throws RouteException
     */
    protected function parseCondition($condition)
    {
        // Allow developers to define non-core conditions by providing a key/value property.
        $conditions = apply_filters('themosisRouteConditions', []);
        $conditions = $this->conditions + $conditions;

        if (isset($conditions[$condition]))
        {
            return $conditions[$condition];
        }

        throw new RouteException('The route condition ['.$condition.'] is no found.');
    }

    /**
     * Find the Closure in an action array.
     *
     * @param array $action
     * @return \Closure
     */
    protected function findClosure(array $action)
    {
        return array_first($action, function($key, $value)
        {
            return is_callable($value);
        });
    }

    /**
     * Get the key / value list of parameters for the route callback/method.
     *
     * @return array
     * @throws \Exception
     */
    public function parameters()
    {
        global $post, $wp_query;

        // Pass WordPress globals to closures or controller methods as parameters.
        $parameters = array_merge($this->parameters, ['post' => $post, 'query' => $wp_query]);

        // When no posts, $post is null.
        // When is null, set the parameter value of $post to false.
        // This avoid missing arguments in methods for routes or controllers.
        if (is_null($parameters['post']))
        {
            $parameters['post'] = false;
        }

        return array_map(function($value)
        {
            return is_string($value) ? rawurldecode($value) : $value;
        }, $parameters);

    }

    /**
     * Get the key / value list of parameters without null values.
     *
     * @return array
     */
    public function parametersWithoutNulls()
    {
        return array_filter($this->parameters(), function($p)
        {
            return !is_null($p);
        });
    }

    /**
     * Run the route action and return the response.
     * A string or a View.
     *
     * @return mixed
     */
    public function run()
    {
        $parameters = array_filter($this->parameters(), function($p) { return isset($p); });

        return call_user_func_array($this->action['uses'], $parameters);
    }

    /**
     * Determine if the route only responds to HTTP requests.
     *
     * @return bool
     */
    public function httpOnly()
    {
        return in_array('http', $this->action, true);
    }

    /**
     * Determine if the route only responds to HTTPS requests.
     *
     * @return bool
     */
    public function httpsOnly()
    {
        return in_array('https', $this->action, true);
    }

    /**
     * Get the HTTP verbs the route responds to.
     *
     * @return array
     */
    public function methods()
    {
        return $this->methods;
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
     * Get the action array for the route.
     *
     * @return array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Return the 'params' value.
     *
     * @return array
     */
    public function getParams()
    {
        return isset($this->action['params']) ? (array) $this->action['params'] : [];
    }

} 