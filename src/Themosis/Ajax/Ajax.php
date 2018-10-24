<?php

namespace Themosis\Ajax;

use Themosis\Hook\IHook;

class Ajax implements AjaxInterface
{
    /**
     * Action instance.
     *
     * @var IHook
     */
    protected $action;

    public function __construct(IHook $action)
    {
        $this->action = $action;
    }

    /**
     * Listen to AJAX API calls.
     *
     * @param string          $action   The AJAX action name.
     * @param \Closure|string $callback A callback function name, a closure or a string defining a class and its method.
     * @param string|bool     $logged   true, false or 'both' type of users.
     *
     * @return AjaxInterface
     */
    public function listen($action, $callback, $logged = 'both'): AjaxInterface
    {
        // Front-end ajax for non-logged users
        // Set $logged to false
        if ($logged === false || $logged === 'no') {
            $this->action->add('wp_ajax_nopriv_'.$action, $callback);
        }

        // Front-end and back-end ajax for logged users
        if ($logged === true || $logged === 'yes') {
            $this->action->add('wp_ajax_'.$action, $callback);
        }

        // Front-end and back-end for both logged in or out users
        if ($logged === 'both') {
            $this->action->add([
                'wp_ajax_nopriv_'.$action,
                'wp_ajax_'.$action
            ], $callback);
        }

        return $this;
    }
}
