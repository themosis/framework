<?php

namespace Themosis\Tests\Forms\Resources\Data;

class ContactFormRequestData
{
    private $fullname;

    private $email;

    private $message;

    private $subscribe;

    private $follow;

    private $colors;

    /**
     * @return mixed
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * @param  mixed  $colors
     */
    public function setColors(array $colors): void
    {
        $this->colors = $colors;
    }

    /**
     * @return mixed
     */
    public function getFollow()
    {
        return $this->follow;
    }

    /**
     * @param  mixed  $follow
     */
    public function setFollow(bool $follow): void
    {
        $this->follow = $follow;
    }

    /**
     * @return mixed
     */
    public function getSubscribe()
    {
        return $this->subscribe;
    }

    /**
     * @param  mixed  $subscribe
     */
    public function setSubscribe(bool $subscribe): void
    {
        $this->subscribe = $subscribe;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param  mixed  $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param  mixed  $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param  mixed  $fullname
     */
    public function setFullname($fullname): void
    {
        $this->fullname = $fullname;
    }
}
