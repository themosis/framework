<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Forms\Contracts\FormBuilderInterface;
use Themosis\Forms\FormBuilder;
use Themosis\Forms\FormFactory;

/**
 * @method static FormBuilderInterface make($dataClass = null, $options = [], $builder = FormBuilder::class)
 *
 * @see FormFactory
 */
class Form extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'form';
    }
}
