<?php
namespace Themosis\Action;

defined('DS') or die('No direct script access.');

class ActionNotifier implements ActionObserver
{
	/**
	 * The event class
	*/
	private $action;

	public function __construct(ActionSubject $action)
	{
		$this->action = $action;
	}

	/**
	 * Execute the event.
	*/
	public function update()
	{
		$this->action->run();
	}
}