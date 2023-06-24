<?php

namespace Themosis\User\Contracts;

use Themosis\User\User;

interface UserFactoryContract
{
    /**
     * Create a new WordPress user and save it to the database.
     */
    public function make(string $username, string $password, string $email): User;

    /**
     * Return the current application user.
     *s
     */
    public function current(): User;

    /**
     * Return a user instance based on given id.
     *s
     */
    public function get(int $user_id): User;
}
