<?php

namespace Themosis\Forms\Resources;

use League\Fractal\TransformerAbstract;

class Factory
{
    /**
     * Return a resource transformer instance.
     *
     * @param $className
     *
     * @return TransformerAbstract
     */
    public function make($className)
    {
        if (false !== strpos($className, '\\')) {
            $class = $className;
        } else {
            $class = 'Themosis\\Forms\\Resources\\Transformers\\'.$className;
        }

        return new $class();
    }
}
