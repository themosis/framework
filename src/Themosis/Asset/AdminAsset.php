<?php
namespace Themosis\Asset;

defined('DS') or die('No direct script access.');

class AdminAsset extends Asset
{
    /**
     * Core framework assets constructor.
     *
     * @param string $type The asset type.
     * @param array $args The asset arguments.
     */
	public function __construct($type, array $args)
	{
        $this->factory = new AssetFactory(true, $type, $args);
	}
}