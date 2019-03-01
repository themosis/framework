<?php

namespace Themosis\Forms\Fields\Contracts;

interface CanHandleUsers
{
    /**
     * Handle field user meta initial value.
     *
     * @param int $user_id
     */
    public function userGet(int $user_id);

    /**
     * Handle field user meta registration.
     *
     * @param string|array $value
     * @param int          $user_id
     */
    public function userSave($value, int $user_id);
}
