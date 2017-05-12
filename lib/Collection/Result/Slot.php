<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\ValueObject;

class Slot extends ValueObject implements ItemInterface
{
    /**
     * Returns the external value ID.
     *
     * @return int|string
     */
    public function getValueId()
    {
        return 0;
    }

    /**
     * Returns the external value type.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'slot';
    }

    /**
     * Returns the external value name.
     *
     * @return string
     */
    public function getName()
    {
        return '(UNKNOWN ITEM)';
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
        return null;
    }
}
