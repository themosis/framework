<?php

namespace Themosis\User\Contracts;

use Themosis\User\User;

interface UserFactoryContract
{
    /**
     * Create a new WordPress user and save it to the database.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     *
     * @return User
     */
    public function make(string $username, string $password, string $email): User;

    /**
     * Return the current application user.
     *s
     *
     * @return User
     */
    public function current(): User;

    /**
     * Return a user instance based on given id.
     *s
     *
     * @param int $user_id
     *
     * @return User
     */
    public function get(int $user_id): User;
}
