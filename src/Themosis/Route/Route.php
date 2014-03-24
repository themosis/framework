<?php
namespace Themosis\Route;

defined('DS') or die('No direct script access.');

class Route extends Router
{
	/**
	 * Route instance infos
	*/
	protected $data;
    
    /**
     * Route constructor. Define a route instance.
     *
     * @param string $callback The Wordpress conditional method name based on the Router class $conds.
     * @param mixed (closure | string) $closure Could be a closure or string defining a controller path.
     * @param array (optional) $terms The Wordpress conditional method parameters, terms like objects slug, id or title,...
     * @param array (optional) $params Additional options in order to control the route. Like 'method', 'template',...
    */
	public function __construct($callback, $closure, $terms = array(), $options = array())
	{
	
		$datas = compact('callback', 'closure', 'terms', 'options');
		$this->data = new RouteData($datas);

		static::$instances[] = $this;
		
	}

	/**
	 * Parse the query and dispatch. Execute Wordpress
	 * conditionals tags to find which page the user is viewing
	 * 
	 * @param string $path The Wordpress conditional method. Specify the method as in the Router conditionals tags.
	 * @param mixed (closure | string) $func The user can pass a closure or string defining a Controller
	 * @param array (associative array) $options The route options like 'method',... see RouteDate class for allowed options.
	 * @return object
	*/
	public static function is($path, $func, $options = array())
	{	
	    /*-----------------------------------------------------------------------*/
	    // Check if givin path is registered in the $conds property in the Router
	    // class.
	    /*-----------------------------------------------------------------------*/
		if (is_string($path) && array_key_exists($path, static::$conds)) {
            
            /*-----------------------------------------------------------------------*/
            // Build the Wordpress conditional method signature.
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
	 * @param string $path The Wordpress conditional method to use.
	 * @param array $terms The Wordpress query terms like the post slug, id, title... check Wordpress codex about conditionals and their values.
	 * @param mixed (closure | string) $func Closure or controller path.
	 * @param array (optional) $options The route options like 'method',...
	 * @return object
	*/
	public static function are($path, $terms, $func, $options = array())
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
	 * Just give an interface if the user wants
	 * to specify ONLY one term.
	 * 
	 * @param string
	 * @param string
	 * @param mixed (closure | string)
	 * @param array (optional) $options The route options like 'method',...
	 * @return object
	*/
	public static function only($path, $term, $func, $options = array())
	{
		$terms = array();

		if (is_string($term) && strlen(trim($term)) > 0) {

			$terms[] = $term;
			static::are($path, $terms, $func, $options);

		} else {
			throw new RouteException("Accept only one term.");
		}
	}

	/**
	 * Used for page template
	 *
	 * @todo Refactor the method to use the $options array...
	 * 
	 * @param string
	 * @param string
	 * @param closure
	*/
	public static function template($path, $term, $func)
	{
		$terms = array();
		$terms[] = $term;

		if (is_string($path) && array_key_exists($path, static::$conds)) {

			$signature = static::$conds[$path];

			if (is_array($terms) && !empty($terms)) {

				if (is_callable($func)) {
                    
                    /*-----------------------------------------------------------------------*/
                    // Send the option 'template' to 'true'
                    /*-----------------------------------------------------------------------*/
					return new static($signature, $func, $terms, array('template' => true));

				} else if (!is_callable($func)) {
					throw new RouteException("Closure expected as a third argument.");
				}

			} else {
				throw new RouteException("Wrong list of terms.");
			}

		} else {
			throw new RouteException("Incorrect Route conditional.");
		}
	}

}