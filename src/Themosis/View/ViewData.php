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
	 * View passsed datas
	*/
	private $datas;

	/**
	 * View ID
	*/
	private $viewID;

	public function __construct($params)
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
	 * @return string
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
	 * Return view compiled content
	 * 
	 * @return string
	*/
	public function get()
	{
		return $this->compiled;
	}

	/**
	 * Return the ViewID
	*/
	public function getViewID()
	{
		return $this->viewID;
	}

	/**
	 * Retrieve passed datas
	*/
	public function getDatas()
	{
		return $this->datas;
	}

	/**
	 * Retrieve view changed value
	*/
	public function hasChanged()
	{
		return $this->hasChanged;
	}

	/**
	 * Allow the view to pass more
	 * datas.
	 * 
	 * @param array
	*/
	public function setDatas($datas)
	{
		$this->datas = array_merge($this->datas, $datas);
	}
}