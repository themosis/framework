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

    /**
     * The ViewRenderer constructor.
     *
     * @param \Themosis\View\ViewData $view The view datas.
     */
	public function __construct(ViewData $view)
	{
		$this->view = $view;
		$this->viewID = $this->view->getViewID();
	}

    /**
     * Evaluate the view and return the output.
     *
     * @throws Exception
     * @return string The view content.
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
     * @TODO Not implemented yet.
	 * @return string The view cached content.
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