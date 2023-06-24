<?php

namespace Themosis\Ajax;

interface AjaxInterface
{
    /**
     * Listen to AJAX calls.
     *
     * @param  string  $action   The AJAX action name.
     * @param  \Closure|string  $callback
     * @param  string|bool  $logged   true, false or 'both' type of users.
     */
    public function listen($action, $callback, $logged = 'both'): AjaxInterface;
}
