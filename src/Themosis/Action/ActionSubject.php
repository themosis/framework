<?php
namespace Themosis\Action;

abstract class ActionSubject
{
	private $observers = array();

    /**
     * Register an observer
     *
     * @param ActionObserver $observer
     * @return void
     */
	public function register(ActionObserver $observer)
	{
		$this->observers[] = $observer;
	}

    /**
     * Trigger each observers.
     *
     * @return void
     */
	protected function notify()
	{
		foreach ($this->observers as $observer) {
			$observer->update();
		}
	}
}