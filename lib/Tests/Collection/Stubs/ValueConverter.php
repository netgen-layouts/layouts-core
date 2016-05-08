<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\Collection\ValueConverterInterface;

class ValueConverter implements ValueConverterInterface
{
    /**
     * Returns if the converter supports the object.
     *
     * @param \Netgen\BlockManager\Tests\Collection\Stubs\Value $object
     *
     * @return bool
     */
    public function supports($object)
    {
        return $object instanceof Value;
    }

    /**
     * Returns the value type for this object.
     *
     * @param \Netgen\BlockManager\Tests\Collection\Stubs\Value $object
     *
     * @return string
     */
    public function getValueType($object)
    {
        return 'value';
    }

    /**
     * Returns the object ID.
     *
     * @param \Netgen\BlockManager\Tests\Collection\Stubs\Value $object
     *
     * @return int|string
     */
    public function getId($object)
    {
        return $object->getId();
    }

    /**
     * Returns the object name.
     *
     * @param \Netgen\BlockManager\Tests\Collection\Stubs\Value $object
     *
     * @return string
     */
    public function getName($object)
    {
        return 'Some value';
    }

    /**
     * Returns if the object is visible.
     *
     * @param \Netgen\BlockManager\Tests\Collection\Stubs\Value $object
     *
     * @return bool
     */
    public function getIsVisible($object)
    {
        return $object->isVisible();
    }
}
