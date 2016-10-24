<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\ValueObject;

class NullItem extends ValueObject implements ItemInterface
{
    /**
     * @var int|string
     */
    protected $valueId;

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
        return 'null';
    }

    /**
     * Returns the external value name.
     *
     * @return string
     */
    public function getName()
    {
        return '(INVALID ITEM)';
    }

    /**
     * Returns if the external value is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Returns the external value object.
     *
     * @return mixed
     */
    public function getObject()
    {
    }
}
