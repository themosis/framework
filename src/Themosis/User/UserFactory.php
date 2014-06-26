<?php
namespace Themosis\User;

class UserFactory {

    /**
     * Create a new WordPress user.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @return \Themosis\User\User
     */
    public function make($username, $password, $email)
    {
        $this->parseCredentials(compact('username', 'password', 'email'));
    }

    /**
     * Check if given credentials to create a new WordPress user are valid.
     *
     * @param array $credentials
     * @throws UserException
     * @return void
     */
    protected function parseCredentials(array $credentials)
    {
        foreach($credentials as $name => $cred)
        {
            if('email' === $name && !is_email($cred)){

                throw new UserException("Invalid user property '{$name}'.");

            }

            if(!is_string($cred) || empty($cred)){

                throw new UserException("Invalid user property '{$name}'.");

            }
        }
    }

} 