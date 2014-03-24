<?php
namespace Themosis\Route;

use Themosis\Configuration\Application;
use Themosis\Action\Action;
use Themosis\View\View;

defined('DS') or die('No direct script access.');

class Router
{
	/**
	* Available Wordpress conditionals
	*/
	protected static $conds = array(
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
		'off'			       => 'themosisIsInMaintenanceMode',
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
		'template'             => 'themosisIsTemplate',
		'time'			       => 'is_time',
		'year'			       => 'is_year'
	);

	/**
	 * Route instances
	*/
	protected static $instances = array();

	public function __construct()
	{
		Action::listen('themosis_render', $this, 'render')->dispatch();
		/*Action::listen('template_redirect', $this, 'render')->dispatch();*/
	}

	/**
	 * Initialize the router
	 *
	 * @return object
	*/
	public static function init()
	{
		return new static();
	}

	/**
	 * Launch the render of each views
	*/
	public function render()
	{
	    /*-----------------------------------------------------------------------*/
	    // Get a reference to the request method
	    /*-----------------------------------------------------------------------*/
	    $request = Request::foundation();
        $httpMethod = $request->getMethod();

	    /*-----------------------------------------------------------------------*/
	    // Check each routes...
	    /*-----------------------------------------------------------------------*/
		foreach (static::$instances as $route) {

		    /*-----------------------------------------------------------------------*/
		    // Dispatch depending of HTTP method - Default is 'ANY'
		    /*-----------------------------------------------------------------------*/
            if ('ANY' === $route->data->getMethod()) {

                /*-----------------------------------------------------------------------*/
                // Nothing special to do, just send the response...
                /*-----------------------------------------------------------------------*/
                static::send($route);

            } else {

                /*-----------------------------------------------------------------------*/
                // Check the request method and use the appropriate route depending
                // of the request HTTP method
                /*-----------------------------------------------------------------------*/
                if ($httpMethod === $route->data->getMethod()) {

                    /*-----------------------------------------------------------------------*/
                    // Send the response...
                    /*-----------------------------------------------------------------------*/
                    static::send($route);

                }

            }
		}
	}

	/**
	 * Use the route instance in order to send
	 * a response to the browser.
	 *
	 * @param object $route The route instance
	*/
	private static function send($route)
	{
		// Check WP conditional
		if (call_user_func($route->data->getCallback(), $route->data->getTerms())) {

			// If the request use a template
			// Render first the template
			if ('themosisIsTemplate' === $route->data->getCallback()) {

				static::handleBeforeOutput($route);

				// If a template is applied and the
				// developer forget to remove the
				// route definition, this prevents
				// the router to render a second view
				// for the same request.
				return;

			} else {

			    /*-----------------------------------------------------------------------*/
			    // Check we're not using a template
			    /*-----------------------------------------------------------------------*/
			    if (static::parseTemplate()) return;

				/*-----------------------------------------------------------------*/
				// Check if we use SSL - Force the page to redirect to the secured
				// one if not set to 'https' by default.
				/*-----------------------------------------------------------------*/
				if ($route->data->getSsl()) {

					if (!is_ssl() && call_user_func($route->data->getCallback(), $route->data->getTerms())) {

						/*-----------------------------------------------------------------*/
						// Stay with a 'permanent' redirection code 301
						// 307 http code preserve the HTTP METHOD
						// however 307 code not well supported by Firefox
						// As for SEO, 307 is still a temporaray redirect, so we have to
						// avoid it.
						/*-----------------------------------------------------------------*/
						wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
				        exit();
   					}

				}

				static::handleBeforeOutput($route);
			}
		}
	}

	/**
	 * Handle output methods. Check if we use a closure
	 * or a controller and send the results for output.
	 *
	 * @param object The Route object
	 * @return void
	*/
	private static function handleBeforeOutput($route)
	{
		// Check if we got a closure or not
		// If not, we're using a controller
		$closure = $route->data->getClosure();
		if (is_callable($closure)) {

			static::output($closure());

		} else {

			list($file, $class, $method) = $route->data->getController();

			// Include the class
			$path = themosis_path('app').'controllers'.DS.$file.CONTROLLER_EXT;
			include_once($path);

			// Call the class and its method
			$tempObject = new $class();

			// Catch the returned value by the method
			$datas = $tempObject->$method();

			// Pass it to output
			static::output($datas);

		}
	}

	/**
	 * If there is a '_themosisPageTemplate' meta data,
     * the page should be rendered with the template route only.
     *
     * @return boolean True if it has a '_themosisPageTemplate'
	*/
	private static function parseTemplate()
	{
		$qo = get_queried_object();

		/*-----------------------------------------------------------------------*/
		// Only check if we are handling a page.
		/*-----------------------------------------------------------------------*/
		if (is_a($qo, 'WP_Post') && 'page' === $qo->post_type) {

    		$meta = get_post_meta($qo->ID, '_themosisPageTemplate', true);

    		if (!empty($meta) && 'none' !== $meta) {

        		return true;

    		}

		}

		return false;
	}

	/**
	 * Render the view.
	 *
	 * @param mixed
	*/
	protected static function output($view)
	{
		if (is_string($view)) {
			echo $view;
		} elseif (is_a($view, 'Themosis\\View\\View')) {
			echo $view->render();
		} else {
			throw new RouteException("Enable to output the requested content.");
		}
	}

	/**
	 * Parse the queries.
	*/
	public static function parse()
	{
		// Retrieve all server vars
		$requests = $_SERVER;

		// Allow developpers to hook and analysed the request
		do_action('themosis_parse_requests', $requests);

	}

	/**
	 * Parse the given controller string.
	 *
	 * @param string
	 * @param boolean
	*/
	public static function valid($controllerPath)
	{
		if (strpos($controllerPath, '@') !== false) {

			return true;

		}

		return false;
	}

	/**
	 * Check if we are calling the API or not.
	 * If it's the API, it makes sure we don't render
	 * another template than the one used by the API class.
	 * NOTE: the query var value is a boolean, defined in the API class
	 * when rules are added.
	 *
	 * @return boolean
	*/
	protected static function isApi()
	{
		$qv = get_query_var(Application::get('api_qv'));

		return $qv;
	}
}