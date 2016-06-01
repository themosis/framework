<?php

namespace Themosis\Hook;

abstract class ActionSubject
{
    private $observers = array();

    /**
     * Register an observer.
     *
     * @param ActionObserver $observer
     */
    public function register(ActionObserver $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * Trigger each observers.
     */
    protected function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update();
        }
    }
}
