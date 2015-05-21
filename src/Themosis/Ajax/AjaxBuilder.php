<?php
namespace Themosis\Ajax;

class AjaxBuilder implements IAjax
{
    public function __construct()
    {
        
    }

    /**
     * Listen to AJAX API calls.
     *
     * @param string $action The AJAX action name.
     * @param string|boolean $logged true, false or 'both' type of users.
     * @param \Closure|string $callback
     * @return \Themosis\Ajax\IAjax
     */
    public function run($action, $logged, $callback)
    {
        // Front-end ajax for non-logged users
        // Set $logged to FALSE
        if ($logged === false || $logged === 'no')
        {
            add_action('wp_ajax_nopriv_'.$action, $callback);
        }

        // Front-end and back-end for logged users
        if ($logged === true || $logged === 'yes')
        {
            add_action('wp_ajax_'.$action, $callback);
        }

        // Front-end and back-end for both logged in or out users
        if ($logged === 'both')
        {
            add_action('wp_ajax_nopriv_'.$action, $callback);
            add_action('wp_ajax_'.$action, $callback);
        }

        return $this;
    }
}