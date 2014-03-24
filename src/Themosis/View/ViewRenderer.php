<?php
namespace Themosis\View;

defined('DS') or die('No direct script access.');

class ViewRenderer
{
	/**
	 * View data object
	*/
	private $view;

	/**
	 * View id
	*/
	private $viewID;

	/**
	 * View temp path
	*/
	protected $path;

	/**
	 * Cached view content
	*/
	protected static $cache = array();

	public function __construct($view)
	{
		$this->view = $view;
		$this->viewID = $this->view->getViewID();
	}

	/**
	 * Start the view renderer
	 * 
	 * @param string
	 * @param array
	 * @return object
	*/
	public static function make($view, $datas = array())
	{
		if (is_string($view) && !empty($view) && is_array($datas)){
			return new static($view, $datas);
		} else {
			throw new ViewException("Enable to render the view.");
		}
	}

	/**
	 * Evaluate the view and return the output
	 * 
	 * @param string
	*/
	public function get()
	{
		// Start output buffer
		ob_start();

		// Extract sent datas
		extract($this->view->getDatas());

		// Grab the view content
		$content = $this->load();

		// Compile the view
		try
		{
			// Eval view content
			eval('?>'.$content);

		} catch (Exception $e)
		{
			ob_get_clean();
			throw $e;
		}

		// Return the compiled view and terminate the output buffer
		return ob_get_clean();
	}

	/**
	 * Load the view content.
	 * 
	 * @return string
	*/
	private function load()
	{
		if (isset(static::$cache[$this->viewID])) {
			return static::$cache[$this->viewID];
		} else {
			return static::$cache[$this->viewID] = $this->view->get();
		}
	}

}