<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Forms\Contracts\FieldTypeInterface;

/**
 * @method static FieldTypeInterface text(string $name, array $options = [])
 * @method static FieldTypeInterface password(string $name, array $options = [])
 * @method static FieldTypeInterface number(string $name, array $options = [])
 * @method static FieldTypeInterface integer(string $name, array $options = [])
 * @method static FieldTypeInterface email(string $name, array $options = [])
 * @method static mixed date($name, array $features = [], array $attributes = [])
 * @method static FieldTypeInterface textarea(string $name, array $options = [])
 * @method static FieldTypeInterface checkbox(string $name, array $options = [])
 * @method static FieldTypeInterface choice(string $name, array $options = [])
 * @method static FieldTypeInterface media($name, array $options = [])
 * @method static FieldTypeInterface editor($name, array $options = [])
 * @method static FieldTypeInterface collection($name, array $options = [])
 * @method static FieldTypeInterface color($name, array $options = [])
 * @method static FieldTypeInterface button(string $name, array $options = [])
 * @method static FieldTypeInterface submit(string $name, array $options = [])
 * @method static FieldTypeInterface hidden(string $name, array $options = [])
 *
 * @see \Themosis\Field\Factory
 */
class Field extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'field';
    }
}
