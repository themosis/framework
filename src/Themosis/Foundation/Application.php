<?php


namespace Themosis\Foundation;


use Illuminate\Container\Container;

class Application extends Container
{
    /**
     * Application constructor.
     *
     * @param string $basePath Base path of framework.
     */
    public function __construct($basePath = '')
    {
        $this->registerBaseBindings();

        $this->registerBaseServiceProviders();

        $this->registerContainerAliases();
    }

    /**
     * Register base dependencies into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);

        // Add application into the container.
        $this->instance('app', $this);

        // Add an extended illuminate container into itself.
        $this->instance('Themosis\Foundation\Application', $this);
    }

    /**
     * Register base service providers if any.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {

    }

    /**
     * Register alias for framework services containers.
     *
     * @return void
     */
    protected function registerContainerAliases()
    {

    }
}