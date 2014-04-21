<?php
namespace Themosis\View;

defined('DS') or die('No direct script access.');

class ViewData
{

	/**
	 * Use engine
	*/
	private $engine = false;

	/**
	 * View file path
	*/
	private $path;

	/**
	 * Compiled content
	*/
	private $compiled;

	/**
	 * View passed datas
	*/
	private $datas;

	/**
	 * View ID
	*/
	private $viewID;

    /**
     * The ViewData constructor.
     *
     * @param array $params The view data parameters.
     */
	public function __construct(array $params)
	{
		$this->path = $params['path'];
		$this->engine = $params['engine'];
		$this->datas = $params['datas'];

		$this->compiled = $this->compile();
		$this->viewID = md5($this->path);
			
	}

	/**
	 * Compile the view content.
	 * 
	 * @return string The converted view content.
	 */
	private function compile()
	{	
		if($this->engine){

			return Scout::parse($this->path);

		} else {

			return file_get_contents($this->path);

		}
	}

	/**
	 * Return view compiled content.
	 * 
	 * @return string The converted view content.
	 */
	public function get()
	{
		return $this->compiled;
	}

    /**
     * Return the ViewID
     *
     * @return string The view ID name.
     */
	public function getViewID()
	{
		return $this->viewID;
	}

	/**
	 * Retrieve passed datas
     *
     * @return array The datas passed to the view.
	 */
	public function getDatas()
	{
		return $this->datas;
	}

	/**
	 * Retrieve view changed value
     *
     * @TODO Not currently in use. Will be used with "cache" methods.
     * @return bool True. False if view is identical to the stored one.
	 */
	public function hasChanged()
	{
		return $this->hasChanged;
	}

	/**
	 * Allow the view to pass more datas.
	 * 
	 * @param array $datas The datas to pass to the view.
	 */
	public function setDatas(array $datas)
	{
		$this->datas = array_merge($this->datas, $datas);
	}
}