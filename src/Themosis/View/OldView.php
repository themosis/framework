<?php
namespace Themosis\View;

defined('DS') or die('No direct script access.');

class OldView extends Viewer
{

	/**
	 * Saved view content
	*/
	protected $view;

	/**
	 * Saved view renderer
	*/
	protected $renderer;

    /**
     * The View constructor.
     *
     * @param string $path The view file path relative to the 'views' directory.
     * @param array $datas The datas to pass to the view.
     * @param bool $engine False by default. True if we use the 'scout' engine.
     * @ignore
     */
	public function __construct($path, array $datas = array(), $engine = false)
	{
		$params = compact('path', 'datas', 'engine');

		$this->view = new ViewData($params);
		$this->renderer = new ViewRenderer($this->view);
	}

    /**
     * Build the view defined with the given path.
     * Users can define variables and pass them for use in the view file.
     *
     * @param string $path The view file path relative to the 'views' directory.
     * @param array $datas The datas to pass to the view.
     * @throws ViewException
     * @return \Themosis\View\View
     */
	public static function make($path, array $datas = array())
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
     *
     * @return void
     * @ignore
	 */
	public function render()
	{	
		return $this->renderer->get();
	}

    /**
     * Allow to add other variables that will be passed to the view.
     *
     * @param array $datas The datas to pass to the view.
     * @throws ViewException
     * @return \Themosis\View\View
     */
	public function with(array $datas)
	{
		if (is_array($datas)) {

			$this->view->setDatas($datas);

			return $this;
			
		} else {
			throw new ViewException("Invalid datas given. Please be sure to pass an associative array.");
		}
	}

}