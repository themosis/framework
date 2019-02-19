<?php

namespace Themosis\Tests\Forms\Resources\Data;

class ContactFormRequestData
{
    private $fullname;

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname): void
    {
        $this->fullname = $fullname;
    }
}
