<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\ValueObject;

class Item extends ValueObject implements ItemInterface
{
    /**
     * @var int|string
     */
    protected $valueId;

    /**
     * @var string
     */
    protected $valueType;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isVisible;

    /**
     * @var mixed
     */
    protected $object;

    public function getValueId()
    {
        return $this->valueId;
    }

    public function getValueType()
    {
        return $this->valueType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isVisible()
    {
        return $this->isVisible;
    }

    public function getObject()
    {
        return $this->object;
    }
}
