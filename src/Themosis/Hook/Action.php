<?php

namespace Themosis\Hook;

class Action extends ActionSubject
{
    /**
     * Hooks references.
     */
    private $hook;

    /**
     * The object where to execute the callback
     * function.
     */
    private $object;

    /**
     * Reference to the callback.
     */
    private $callback;

    /**
     * The notifier which execute the event.
     */
    private $notifier;

    /**
     * Params of the callback.
     */
    private $params;

    /**
     * Additional arguments passed.
     */
    private $args;

    /**
     * Used to interact with the WP core action hooks
     * system. You'll need to provide which hooks you're
     * "listening to" and the callback function
     * which will be processed by the object.
     *
     * @param string $hook
     * @param string $object
     * @param string $callback
     * @param mixed  $args
     * @ignore
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
     * @param string     $hook     The action/hook name
     * @param string     $object   The class instance name to use
     * @param string     $callback The instance method to call
     * @param null|mixed $args     The add_action extra arguments
     *
     * @return static An Action instance
     */
    public static function listen($hook, $object, $callback, $args = null)
    {
        return new static($hook, $object, $callback, $args);
    }

    /**
     * Run by the client in order to dispatch
     * the registered hook.
     * THIS IS WHAT TRIGGERS EVERYTHING.
     */
    public function dispatch()
    {
        add_action($this->hook, array($this, 'action'));
    }

    /**
     * Hook method.
     *
     * @param null|mixed $params The associated parameters given by the WordPress hook.
     * @ignore
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
     * @ignore
     */
    public function run()
    {
        $signature = $this->callback;

        return $this->object->$signature($this->params, $this->args);
    }
}
