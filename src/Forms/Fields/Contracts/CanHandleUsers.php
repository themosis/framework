<?php

namespace Themosis\Forms\Fields\Contracts;

interface CanHandleUsers
{
    /**
     * Handle field user meta initial value.
     */
    public function userGet(int $user_id);

    /**
     * Handle field user meta registration.
     *
     * @param  string|array  $value
     */
    public function userSave($value, int $user_id);
}
