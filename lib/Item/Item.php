<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\ValueObject;

class Item extends ValueObject
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

    /**
     * Returns the external value ID.
     *
     * @return int|string
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    /**
     * Returns the external value type.
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     * Returns the external value name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns if the external value is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * Returns the external value object.
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }
}
