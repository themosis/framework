<?php

namespace Themosis\Forms\DataMappers;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;

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
     * Map data from object to field.
     *
     * @param mixed              $data
     * @param FieldTypeInterface $field
     */
    public function mapFromObjectToField($data, FieldTypeInterface $field)
    {
        if (! is_object($data)) {
            $this->triggerException();
        }

        $field->setValue(
            $this->propertyAccessor->getValue($data, $field->getBaseName())
        );
    }

    /**
     * Map data from field to object.
     *
     * @param FieldTypeInterface $field
     * @param mixed              $data
     */
    public function mapFromFieldToObject(FieldTypeInterface $field, $data)
    {
        if (! is_object($data)) {
            $this->triggerException();
        }

        $this->propertyAccessor->setValue($data, $field->getBaseName(), $field->getValue());
    }

    /**
     * Trigger invalid argument exception if data
     * reference is not a PHP object instance.
     */
    private function triggerException()
    {
        throw new \InvalidArgumentException('The data object must be a PHP object instance.');
    }
}
