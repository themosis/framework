<?php

/**********************************************************************/
// GLOBALS
/**********************************************************************/
$wp_actions = [];

/**********************************************************************/
// HOOKS
/**********************************************************************/
function add_action()
{
}

function do_action($tag, $args = null)
{
    global $wp_actions;

    if (! isset($wp_actions[$tag])) {
        $wp_actions[$tag] = 1;
    } else {
        ++$wp_actions[$tag];
    }
}

function do_action_ref_array($tag, $args)
{
    do_action($tag, $args);
}

function did_action($tag)
{
    global $wp_actions;

    if (!isset($wp_actions[$tag])) {
        return 0;
    }

    return $wp_actions[$tag];
}

function add_filter()
{
}

// Mocked functions for tests
function actionHookCallback()
{
}

function callingForUncharted()
{
}
