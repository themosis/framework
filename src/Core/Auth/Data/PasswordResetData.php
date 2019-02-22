<?php

namespace Themosis\Core\Auth\Data;

class PasswordResetData
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $passwordConfirmation;

    /**
     * @return string
     */
    public function getToken(): string
    {
        if (is_null($this->token)) {
            return '';
        }

        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        if (is_null($this->email)) {
            return '';
        }

        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        if (is_null($this->password)) {
            return '';
        }

        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPasswordConfirmation(): string
    {
        if (is_null($this->passwordConfirmation)) {
            return '';
        }

        return $this->passwordConfirmation;
    }

    /**
     * @param string $passwordConfirmation
     */
    public function setPasswordConfirmation(string $passwordConfirmation): void
    {
        $this->passwordConfirmation = $passwordConfirmation;
    }
}
