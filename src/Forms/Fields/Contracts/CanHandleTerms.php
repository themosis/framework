<?php

namespace Themosis\Forms\Fields\Contracts;

interface CanHandleTerms
{
    /**
     * Handle field term meta registration.
     *
     * @param mixed $value
     * @param int   $term_id
     */
    public function termSave($value, int $term_id);

    /**
     * Handle field term meta initial value.
     *
     * @param int $term_id
     */
    public function termGet(int $term_id);
}
