<?php
namespace Themosis\Core;

abstract class IgniterService {

    /**
     * The application instance.
     *
     * @var \Themosis\Core\Application
     */
    protected $app;

    /**
     * Create an igniter service.
     *
     * @param \Themosis\Core\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Ignite a service.
     *
     * @return void
     */
    abstract public function ignite();

} 