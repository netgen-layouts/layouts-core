<?php

namespace Netgen\BlockManager\Item\ValueConverter;

use Netgen\BlockManager\Item\ValueConverterInterface;

class NullValueConverter implements ValueConverterInterface
{
    /**
     * Returns if the converter supports the object.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function supports($object)
    {
        return $object === null;
    }

    /**
     * Returns the value type for this object.
     *
     * @param mixed $object
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
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     *
     * @return int|string
     */
    public function getId($object)
    {
        return 0;
    }

    /**
     * Returns the object name.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
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
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     *
     * @return bool
     */
    public function getIsVisible($object)
    {
        return true;
    }
}
