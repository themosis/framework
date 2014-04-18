<?php
namespace Themosis\Metabox;

defined('DS') or die('No direct script access.');

class MetaboxData
{
	/**
	 * All metabox datas
	*/
	private $datas = array();

	/**
	 * Set the datas for the associated metabox.
	 * 
	 * @param array $datas The fields of the metabox.
	 */
	public function set(array $datas)
	{
		$this->datas = $datas;
	}

	/**
	 * Get the datas of the metabox
	 * 
	 * @return array The metabox fields.
	 */
	public function get()
	{
		return $this->datas;
	}
}