<?php
namespace Themosis\Core;

class Application extends Container {

    /**
     * Register all igniter services classes.
     *
     * @return void
     */
    public function registerCoreIgniters()
    {
        $services = array(

            'form'          => '\Themosis\Html\FormIgniterService'

        );

        foreach($services as $key => $value){

            /**
             * Register the instance name.
             * The facade call the appropriate igniterService.
             */
            $this->igniters[$key] = $value;

        }
    }

    /**
     * Retrieve the igniter class name.
     *
     * @param $key The igniter key name
     * @return string
     */
    public function getIgniter($key)
    {
        return $this->igniters[$key];
    }

    /**
     * Add the instance to the application.
     *
     * @param string $key The facade key.
     * @param callable $closure The function that call the needed instance.
     * @return void
     */
    public function bind($key, Callable $closure)
    {
        $this->instances[$key] = $closure();
    }

} 