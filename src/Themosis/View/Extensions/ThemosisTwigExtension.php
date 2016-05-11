<?php

namespace Themosis\View\Extensions;

class ThemosisTwigExtension
{
    /**
     * Allow developers to call core php and WordPress functions
     * using the `wp` namespace inside their templates.
     * 
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, array $arguments)
    {
        call_user_func_array($name, $arguments);
    }
}
