<?php
namespace Themosis\Ajax;

interface IAjax
{
    /**
     * Listen to AJAX API calls.
     *
     * @param string $action The AJAX action name.
     * @param string|boolean $logged true, false or 'both' type of users.
     * @param \Closure|string $callback
     */
    public function run($action, $logged, $callback);
}