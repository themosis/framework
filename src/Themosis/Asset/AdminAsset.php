<?php
namespace Themosis\Asset;

defined('DS') or die('No direct script access.');

class AdminAsset extends Asset
{
	public function __construct($type, $args)
	{
		$this->factory = new AssetFactory(true, $type, $args);
	}
}