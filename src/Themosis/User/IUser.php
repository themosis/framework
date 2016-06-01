<?php

namespace Themosis\User;

interface IUser
{
    /**
     * Create a new WordPress user.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     *
     * @return \Themosis\User\User|\WP_Error
     */
    public function make($username, $password, $email);

    /**
     * Look at the current user and return an instance.
     *
     * @return \Themosis\User\User
     */
    public function current();

    /**
     * Return a User instance using its ID.
     *
     * @param int $id
     *
     * @return \Themosis\User\User
     */
    public function get($id);

    /**
     * Register sections for user custom fields.
     *
     * @param array $sections A list of sections to register.
     *
     * @return \Themosis\User\IUser
     */
    public function addSections(array $sections);

    /**
     * Register custom fields for users.
     *
     * @param array  $fields     The user custom fields. By sections or not.
     * @param string $capability The minimum capability in order to save custom fields data.
     *
     * @return \Themosis\User\IUser
     */
    public function addFields(array $fields, $capability = 'edit_users');

    /**
     * Register validation rules for user custom fields.
     *
     * @param array $rules
     *
     * @return \Themosis\User\IUser
     */
    public function validate(array $rules = []);
}
