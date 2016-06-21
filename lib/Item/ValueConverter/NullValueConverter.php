<?php

namespace Netgen\BlockManager\Item\ValueConverter;

use Netgen\BlockManager\Item\NullValue;
use Netgen\BlockManager\Item\ValueConverterInterface;

class NullValueConverter implements ValueConverterInterface
{
    /**
     * Returns if the converter supports the object.
     *
     * @param \Netgen\BlockManager\Item\NullValue $object
     *
     * @return bool
     */
    public function supports($object)
    {
        return $object instanceof NullValue;
    }

    /**
     * Returns the value type for this object.
     *
     * @param \Netgen\BlockManager\Item\NullValue $object
     *
     * @return string
     */
    public function getValueType($object)
    {
        return 'null';
    }

    /**
     * Returns the object ID.
     *
     * @param \Netgen\BlockManager\Item\NullValue $object
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
     * @param \Netgen\BlockManager\Item\NullValue $object
     *
     * @return string
     */
    public function getName($object)
    {
        return '(INVALID ITEM)';
    }

    /**
     * Returns if the object is visible.
     *
     * @param \Netgen\BlockManager\Item\NullValue $object
     *
     * @return bool
     */
    public function getIsVisible($object)
    {
        return true;
    }
}
