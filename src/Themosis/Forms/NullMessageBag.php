<?php

namespace Themosis\Forms;

use Illuminate\Contracts\Support\MessageBag;

class NullMessageBag implements MessageBag
{
    /**
     * @inheritdoc
     *
     * @return array
     */
    public function toArray()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function keys()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @param string $key
     * @param string $message
     *
     * @return $this|MessageBag
     */
    public function add($key, $message)
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param array|\Illuminate\Contracts\Support\MessageProvider $messages
     *
     * @return $this|MessageBag
     */
    public function merge($messages)
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param array|string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return false;
    }

    /**
     * @inheritdoc
     *
     * @param null $key
     * @param null $format
     *
     * @return null|string
     */
    public function first($key = null, $format = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     *
     * @param string $key
     * @param null   $format
     *
     * @return array
     */
    public function get($key, $format = null)
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @param null $format
     *
     * @return array
     */
    public function all($format = null)
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getMessages()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return null|string
     */
    public function getFormat()
    {
        return null;
    }

    /**
     * @inheritdoc
     *
     * @param string $format
     *
     * @return $this|MessageBag
     */
    public function setFormat($format = ':message')
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function isEmpty()
    {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    /**
     * @inheritdoc
     *
     * @return int
     */
    public function count()
    {
        return 0;
    }

    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function any()
    {
        return $this->count() > 0;
    }
}
