<?php

namespace Themosis\Forms\DataMappers;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class DataMapperManager
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Return the property accessor instance.
     *
     * @return PropertyAccessorInterface
     */
    public function getAccessor()
    {
        return $this->propertyAccessor;
    }
}
