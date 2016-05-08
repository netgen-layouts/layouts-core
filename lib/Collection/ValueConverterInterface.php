<?php

namespace Netgen\BlockManager\Collection;

interface ValueConverterInterface
{
    /**
     * Returns if the converter supports the object.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function supports($object);

    /**
     * Returns the value type for this object.
     *
     * @param mixed $object
     *
     * @return string
     */
    public function getValueType($object);

    /**
     * Returns the object ID.
     *
     * @param mixed $object
     *
     * @return int|string
     */
    public function getId($object);

    /**
     * Returns the object name.
     *
     * @param mixed $object
     *
     * @return string
     */
    public function getName($object);

    /**
     * Returns if the object is visible.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function getIsVisible($object);
}
