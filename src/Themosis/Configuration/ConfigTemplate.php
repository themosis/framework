<?php
namespace Themosis\Configuration;

defined('DS') or die('No direct script access.');

abstract class ConfigTemplate
{
	/**
	 * Save the retrieved datas
	*/
	protected static $datas = array();

	/**
	 * Retrieve and set the datas returned
	 * by the include function.
	 * 
	 * @param string
	*/
	public function set($path)
	{
		static::$datas = include($path);
	}

	/**
	 * Retrieve the application property.
	 * If it doesn't exist, return null.
	 * 
	 * @param string
	 * @return mixed
	*/
	public static function get($property)
	{
		if (array_key_exists($property, static::$datas)) {

			return static::$datas[$property];

		}

		return null;
	}
}