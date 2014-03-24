<?php
namespace Themosis\View;

defined('DS') or die('No direct script access.');

class View extends Viewer
{

	/**
	 * Saved view content
	*/
	protected $view;

	/**
	 * Saved view renderer
	*/
	protected $renderer;

	public function __construct($path, $datas = array(), $engine = false)
	{
		$params = compact('path', 'datas', 'engine');

		$this->view = new ViewData($params);
		$this->renderer = new ViewRenderer($this->view);
	}

	/**
	 * Build the view defined with the given
	 * path. Users can define variables and pass
	 * them for use in the view file.
	 * 
	 * @param string
	 * @param array (optional)
	 * @return object
	*/
	public static function make($path, $datas = array())
	{
		if (is_string($path) && strlen(trim($path)) > 0) {

			$path = static::parsePath($path);

			return static::parse($path, $datas);

		} else {
			throw new ViewException("Invalid view parameters for the View::make method.");
		}
	}

	/**
	 * Render the requested view.
	*/
	public function render()
	{	
		return $this->renderer->get();
	}

	/**
	 * Allow to add other variable that will be
	 * passed to the view.
	 * 
	 * @param array
	 * @return object
	*/
	public function with($datas)
	{
		if (is_array($datas)) {

			$this->view->setDatas($datas);

			return $this;
			
		} else {
			throw new ViewException("Invalid datas given. Please be sure to pass an associative array.");
		}
	}

}