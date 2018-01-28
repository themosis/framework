<?php

include 'AjaxCustom.php';

class AjaxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Action hook instance.
     *
     * @var \Themosis\Hook\ActionBuilder
     */
    protected $action;

    public function setUp()
    {
        $this->action = new \Themosis\Hook\ActionBuilder(new \Themosis\Foundation\Application());
    }

    public function testAjaxIsListeningUsingClosure()
    {
        $ajax = new \Themosis\Ajax\AjaxBuilder($this->action);

        // Listen to an ajax request on logged in and out users (default).
        $ajax->listen('infinite-scroll', function () {
        });

        // Check the action is registered.
        $this->assertTrue($this->action->exists('wp_ajax_nopriv_infinite-scroll'));
        $this->assertTrue($this->action->exists('wp_ajax_infinite-scroll'));

        // Check the action callback is a Closure.
        $this->assertInstanceOf('\Closure', $this->action->getCallback('wp_ajax_nopriv_infinite-scroll')[0]);
        $this->assertInstanceOf('\Closure', $this->action->getCallback('wp_ajax_infinite-scroll')[0]);
    }

    public function testAjaxIsListeningUsingNamedCallback()
    {
        $ajax = new \Themosis\Ajax\AjaxBuilder($this->action);

        // Listen to an ajax request on logged in users only.
        $ajax->listen('update-list', 'ajaxCallback', true);

        // Check the action is registered.
        $this->assertTrue($this->action->exists('wp_ajax_update-list'));
        $this->assertFalse($this->action->exists('wp_ajax_nopriv_update-list'));

        // Check the action callback is callable.
        $this->assertTrue(is_callable($this->action->getCallback('wp_ajax_update-list')[0]));
    }

    public function testAjaxIsListeningUsingClass()
    {
        $ajax = new \Themosis\Ajax\AjaxBuilder($this->action);

        // Listen to an ajac request on logged out users only using a Class.
        $ajax->listen('change-state', 'AjaxCustom@changeState', false);

        // Check the action is registered.
        $this->assertTrue($this->action->exists('wp_ajax_nopriv_change-state'));
        $this->assertFalse($this->action->exists('wp_ajax_change-state'));

        // Check the ajax callback is an array with an instance of AjaxCustom class.
        $class = new AjaxCustom();
        $this->assertEquals([$class, 'changeState'], $this->action->getCallback('wp_ajax_nopriv_change-state')[0]);
    }
}
