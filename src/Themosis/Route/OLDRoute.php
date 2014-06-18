<?php
namespace Themosis\Route;

defined('DS') or die('No direct script access.');

class OLDRoute extends Router
{
	/**
	 * Route instance info
	*/
	protected $data;

    /**
     * Route constructor. Define a route instance.
     *
     * @param string $callback The WordPress conditional method name based on the Router class $conds property.
     * @param callable|string $closure Could be a closure or string defining a controller path.
     * @param array $terms The WordPress conditional method parameters: terms like objects slug, id, title,...
     * @param array $options The Route options.
     * @ignore
     */
	public function __construct($callback, $closure, array $terms = array(), array $options = array())
	{
		$datas = compact('callback', 'closure', 'terms', 'options');
		$this->data = new RouteData($datas);

		static::$instances[] = $this;
	}

    /**
     * Parse the query and dispatch. Execute WordPress
     * conditionals tags to find which page the user is viewing
     *
     * @param string $path The WordPress conditional method.
     * @param callable|string $func The user can pass a closure or a string defining a controller path.
     * @param array $options The route options like 'method'. See RouteData class for allowed options.
     * @throws RouteException
     * @return \Themosis\Route\Route
     */
	public static function is($path, $func, array $options = array())
	{	
	    /*-----------------------------------------------------------------------*/
	    // Check if given path is registered in the $conds property in the Router
	    // class.
	    /*-----------------------------------------------------------------------*/
		if (is_string($path) && array_key_exists($path, static::$conds)) {
            
            /*-----------------------------------------------------------------------*/
            // Build the WordPress conditional method signature.
            /*-----------------------------------------------------------------------*/
			$signature = static::$conds[$path];
            
            /*-----------------------------------------------------------------------*/
            // If it's callable, it's closure...
            /*-----------------------------------------------------------------------*/
			if (is_callable($func)) {

				return new static($signature, $func, array(), $options);
            
            /*-----------------------------------------------------------------------*/
            // If it's not callable, check if it is a string and valid one for
            // defining a controller path.
            /*-----------------------------------------------------------------------*/
			} elseif(is_string($func) && static::valid($func)) {

				return new static($signature, $func, array(), $options);

			} elseif (!is_callable($func)) {
			
				throw new RouteException("Closure expected as a second argument.");
				
			}

		} else {
		
			throw new RouteException("Incorrect Route conditional.");
			
		}

	}

    /**
     * Same as 'is' excepts that you can fine tuned your request
     * by passing an array of searchable query terms.
     *
     * @param string $path The WordPress conditional method to use.
     * @param array $terms The WordPress query terms: the post slug, id, title,...
     * @param callable|string $func Closure or controller path.
     * @param array $options The route options like 'method',...
     * @throws RouteException
     * @return \Themosis\Route\Route
     */
	public static function are($path, array $terms, $func, array $options = array())
	{
		if (is_string($path) && array_key_exists($path, static::$conds)) {

			$signature = static::$conds[$path];

			if (is_array($terms) && !empty($terms)) {

				if (is_callable($func)) {

					return new static($signature, $func, $terms, $options);

				} elseif (is_string($func) && static::valid($func)){

					return new static($signature, $func, $terms, $options);

				} elseif (!is_callable($func)) {
					throw new RouteException("Closure expected as a third argument.");
				}

			} else {
				throw new RouteException("Wrong list of terms.");
			}

		} else {
			throw new RouteException("Incorrect Route conditional.");
		}
	}

    /**
     * Gives an interface if the user wants
     * to specify only ONE term.
     *
     * @param string $path The conditional to use.
     * @param string $term The term parameter for the conditional.
     * @param callable|string $func A closure or a controller path.
     * @param array $options The route options like 'method',...
     * @throws RouteException
     * @return \Themosis\Route\Route
     */
	public static function only($path, $term, $func, array $options = array())
	{
		$terms = array();

		if (is_string($term) && strlen(trim($term)) > 0) {

			$terms[] = $term;
			return static::are($path, $terms, $func, $options);

		} else {
			throw new RouteException("Accept only one term.");
		}
	}

}