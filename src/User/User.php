<?php

namespace Themosis\User;

use Themosis\User\Exceptions\UserException;

class User extends \WP_User
{
    /**
     * Check if the user has role.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->has_cap($role);
    }

    /**
     * Set User role.
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role): User
    {
        $this->set_role($role);

        return $this;
    }

    /**
     * Check if the user can do a defined capability.
     *
     * @param string $cap
     *
     * @return bool
     */
    public function can($cap)
    {
        return $this->has_cap(...func_get_args());
    }

    /**
     * Update user properties.
     *
     * @param array $data
     *
     * @throws UserException
     *
     * @return User
     */
    public function update(array $data): User
    {
        $user = wp_update_user(array_merge($data, [
            'ID' => $this->ID
        ]));

        if (is_a($user, 'WP_Error')) {
            throw new UserException($user->get_error_message());
        }

        return $this;
    }

    /**
     * Update single user meta data.
     *
     * @param string $key
     * @param string $value
     *
     * @throws UserException
     *
     * @return User
     */
    public function updateMetaData(string $key, string $value): User
    {
        $previous = get_user_meta($this->ID, $key, true);

        if ($previous === $value) {
            return $this;
        }

        $update = update_user_meta($this->ID, $key, $value, $previous);

        if (false === $update) {
            throw new UserException("Cannot update user meta data with a key of [$key]");
        }

        return $this;
    }
}
