<?php
namespace Themosis\Ajax;

use Themosis\Action\IAction;

class AjaxBuilder implements IAjax
{
    /**
     * Action instance.
     */
    protected $action;

    public function __construct(IAction $action)
    {
        $this->action = $action;
    }

    /**
     * Listen to AJAX API calls.
     *
     * @param string $name The AJAX action name.
     * @param string|boolean $logged true, false or 'both' type of users.
     * @param \Closure|string $callback
     * @return \Themosis\Ajax\IAjax
     */
    public function run($name, $logged, $callback)
    {
        // Front-end ajax for non-logged users
        // Set $logged to false
        if ($logged === false || $logged === 'no')
        {
            $this->action->add('wp_ajax_nopriv_'.$name, $callback);
        }

        // Front-end and back-end ajax for logged users
        if ($logged === true || $logged === 'yes')
        {
            $this->action->add('wp_ajax_'.$name, $callback);
        }

        // Front-end and back-end for both logged in or out users
        if ($logged === 'both')
        {
            $this->action->add('wp_ajax_nopriv_'.$name, $callback);
            $this->action->add('wp_ajax_'.$name, $callback);
        }

        return $this;
    }
}