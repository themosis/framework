<?php
namespace Themosis\Action;

defined('DS') or die('No direct script access.');

interface ActionObserver
{
	public function update();
}