<?php

namespace Themosis\User;

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
    public function setRole($role)
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
        return $this->has_cap($cap);
    }

    /**
     * Update the user properties.
     *
     * @param array $userdata
     *
     * @return \Themosis\User\User|\WP_Error
     */
    public function update(array $userdata)
    {
        $userdata = array_merge($userdata, ['ID' => $this->ID]);

        $user = wp_update_user($userdata);

        if (is_wp_error($user)) {
            return $user;
        }

        return $this;
    }
}
