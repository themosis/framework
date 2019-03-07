<?php

namespace Themosis\User;

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Themosis\User\Contracts\UserFactoryContract;
use Themosis\User\Exceptions\DuplicateUserException;
use Themosis\User\Exceptions\UserException;

class Factory implements UserFactoryContract
{
    /**
     * @var ValidatorFactory
     */
    protected $validator;

    public function __construct(ValidatorFactory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Create a WordPress user and save it to the database.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     *
     * @throws UserException
     * @throws DuplicateUserException
     *
     * @return User
     */
    public function make(string $username, string $password, string $email): User
    {
        $this->validate(compact('username', 'password', 'email'));

        $user = wp_create_user($username, $password, $email);

        if (is_a($user, 'WP_Error')) {
            if ('existing_user_login' === $user->get_error_code()) {
                throw new DuplicateUserException($user->get_error_message());
            }

            throw new UserException($user->get_error_message());
        }

        return $this->get($user);
    }

    /**
     * Return the current application user.
     *
     * @return User
     */
    public function current(): User
    {
        $user = wp_get_current_user();

        return $this->get($user->ID);
    }

    /**
     * Return a user instance based on given id.
     *
     * @param int $user_id
     *
     * @return User
     */
    public function get(int $user_id): User
    {
        return new User($user_id);
    }

    /**
     * Validate user credentials.
     *
     * @param array $data
     *
     * @throws UserException
     */
    protected function validate(array $data)
    {
        $validator = $this->validator->make(
            $data,
            [
                'username' => 'min:6|max:60',
                'password' => 'min:6|max:255',
                'email' => 'email|max:100'
            ]
        );

        if ($validator->fails()) {
            $message = sprintf(
                'Invalid user credentials. %s',
                implode(' ', $validator->errors()->all())
            );
            throw new UserException($message);
        }
    }
}
