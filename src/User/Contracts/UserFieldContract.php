<?php

namespace Themosis\User\Contracts;

use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Support\Contracts\SectionInterface;

interface UserFieldContract
{
    /**
     * Make user fields.
     */
    public function make(array $options = []): UserFieldContract;

    /**
     * Add a user field.
     *
     * @param  FieldTypeInterface|SectionInterface  $field
     */
    public function add($field, SectionInterface $section = null): UserFieldContract;

    /**
     * Set the user fields.
     */
    public function set(): UserFieldContract;

    /**
     * Return the fields repository.
     */
    public function repository(): FieldsRepositoryInterface;
}
