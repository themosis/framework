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

            'field'         => '\Themosis\Field\FieldIgniterService',
            'form'          => '\Themosis\Html\FormIgniterService',
            'html'          => '\Themosis\Html\HtmlIgniterService',
            'metabox'       => '\Themosis\Metabox\MetaboxIgniterService'

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
     * Add the instance to the application.
     *
     * @param string $key The facade key.
     * @param callable $closure The function that call the needed instance.
     * @return void
     */
    public function bind($key, Callable $closure)
    {
        // Send the application instance to the closure.
        // Allows the container to call the dependencies.
        $this->instances[$key] = $closure($this);
    }

} 