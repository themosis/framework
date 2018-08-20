<?php

namespace Themosis\Metabox\Resources;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;

class MetaboxResource implements MetaboxResourceInterface
{
    /**
     * @var mixed
     */
    protected $source;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var SerializerAbstract
     */
    protected $serializer;

    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    public function __construct(Manager $manager, SerializerAbstract $serializer, TransformerAbstract $transformer)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->transformer = $transformer;
    }

    /**
     * Return the fractal resource instance.
     *
     * @return Item
     */
    protected function resource()
    {
        return new Item($this->source, $this->transformer);
    }

    /**
     * Define the serialization method.
     *
     * @return $this
     */
    protected function serialize()
    {
        $this->manager->setSerializer($this->serializer);

        return $this;
    }

    /**
     * Set the metabox data source element.
     *
     * @param mixed $source
     *
     * @return MetaboxResourceInterface
     */
    public function setSource($source): MetaboxResourceInterface
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Return an array representation of the data source.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->serialize()->manager->createData($this->resource())->toArray();
    }

    /**
     * Return a JSON representation of the data source.
     *
     * @return string
     */
    public function toJson(): string
    {
        return $this->serialize()->manager->createData($this->resource())->toJson();
    }
}
