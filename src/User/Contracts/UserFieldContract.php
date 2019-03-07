<?php

namespace Themosis\User\Contracts;

use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Support\Contracts\SectionInterface;

interface UserFieldContract
{
    /**
     * Make user fields.
     *
     * @param array $options
     *
     * @return UserFieldContract
     */
    public function make(array $options = []): UserFieldContract;

    /**
     * Add a user field.
     *
     * @param FieldTypeInterface|SectionInterface $field
     * @param SectionInterface|null               $section
     *
     * @return UserFieldContract
     */
    public function add($field, SectionInterface $section = null): UserFieldContract;

    /**
     * Set the user fields.
     *
     * @return UserFieldContract
     */
    public function set(): UserFieldContract;

    /**
     * Return the fields repository.
     *
     * @return FieldsRepositoryInterface
     */
    public function repository(): FieldsRepositoryInterface;
}
