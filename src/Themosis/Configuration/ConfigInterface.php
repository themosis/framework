<?php
namespace Themosis\Configuration;

interface ConfigInterface
{
	/**
	 * Used to saved the retrieved datas from a given path
	 * 
	 * @param string $path
	 */
	public function set($path);

	/**
	 * Depending of the child class, will install the given
	 * config properties.
	 */
	public function install();

}