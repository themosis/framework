<?php
namespace Themosis\Core;

class AdminLoader extends Loader
{
	public function __construct($path)
	{
        $this->append($path);
	}
}