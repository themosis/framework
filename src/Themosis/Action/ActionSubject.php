<?php
namespace Themosis\Action;

defined('DS') or die('No direct script access.');

abstract class ActionSubject
{
	private $observers = array();

	public function register(ActionObserver $observer)
	{
		$this->observers[] = $observer;
	}

	protected function notify()
	{
		foreach ($this->observers as $observer) {
			$observer->update();
		}
	}
}