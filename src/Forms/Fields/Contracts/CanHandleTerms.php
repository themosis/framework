<?php

namespace Themosis\Forms\Fields\Contracts;

interface CanHandleTerms
{
    /**
     * Handle field term meta registration.
     *
     * @param  mixed  $value
     */
    public function termSave($value, int $term_id);

    /**
     * Handle field term meta initial value.
     */
    public function termGet(int $term_id);
}
