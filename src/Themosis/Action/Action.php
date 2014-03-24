<?php
namespace Themosis\Action;

defined('DS') or die('No direct script access.');

class Action extends ActionSubject
{
	/**
	 * Hooks references
	*/
	private $hook;

	/**
	 * The object where to execute the callback
	 * function.
	*/
	private $object;

	/**
	 * Reference to the callback
	*/
	private $callback;

	/**
	 * The notifier which execute the event
	*/
	private $notifier;

	/**
	 * Params of the callback
	*/
	private $params;

	/**
	 * Additional arguments passed
	*/
	private $args;

	/**
	 * Used to interact with the WP core action hooks
	 * system. You'll need to provide which hooks you're
	 * "listening to" and the callback function
	 * which will be processed by the object.
	 * 
	 * @param string
	 * @param object
	 * @param string
	 * @param mixed
	*/
	public function __construct($hook, $object, $callback, $args = null)
	{
		$this->hook = $hook;
		$this->object = $object;
		$this->callback = $callback;
		$this->args = $args;
		
		$this->notifier = new ActionNotifier($this); // Observer - notifier
		$this->register($this->notifier);
	}

	/**
	 * Launch / Listen to an event / hook.
	 * 
	 * @param string
	 * @param object
	 * @param string
	 * @param mixed (additional parameters)
	*/
	public static function listen($hook, $object, $callback, $args = null)
	{
		return new static($hook, $object, $callback, $args);
	}

	/**
	 * Run by the client in order to dispatch
	 * the registered hook.
	 * THIS IS WHAT TRIGGER EVERYTHING
	*/
	public function dispatch()
	{
		$args = $this->args;

		add_action($this->hook, array(&$this, 'action'));
	}

	/**
	 * Hook method
	 * 
	 * @param mixed
	*/
	public function action($params = null)
	{
		$this->params = $params;
		$this->notify();
	}

	/**
	 * Execute the callback function associated
	 * to the given object.
	 * 
	 * @return string
	*/
	public function run()
	{
		$signature = $this->callback;
		$this->object->$signature($this->params, $this->args);
	}
}