<?php

namespace Themosis\Forms\Fields\Types;

class CsrfType extends HiddenType
{
    /**
     * Crsf field name is prefix free.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getBaseName();
    }
}
