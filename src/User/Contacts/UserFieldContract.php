<?php

namespace Themosis\User\Contacts;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Support\Contracts\SectionInterface;

interface UserFieldContract
{
    /**
     * Add a user field.
     *
     * @param FieldTypeInterface|SectionInterface $field
     * @param SectionInterface|null               $section
     *
     * @return UserFieldContract
     */
    public function add($field, SectionInterface $section = null): UserFieldContract;
}
