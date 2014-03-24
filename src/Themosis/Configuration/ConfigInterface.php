<?php
namespace Themosis\Configuration;

defined('DS') or die('No direct script access.');

interface ConfigInterface
{
	/**
	 * Used to saved the retrieved datas from a given path
	 * 
	 * @param string
	*/
	public function set($path);

	/**
	 * Depending of the child class, will install the given
	 * config properties.
	*/
	public function install();

}