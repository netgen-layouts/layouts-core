<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

/**
 * Value converter is used to convert the loaded CMS value to an instance of CmsItemInterface.
 * This is achieved by providing information (ID, name, visibility...) used by
 * the item builder service which actually builds the item.
 */
interface ValueConverterInterface
{
    /**
     * Returns if the converter supports the object.
     *
     * @param object $object
     *
     * @return bool
     */
    public function supports($object): bool;

    /**
     * Returns the value type for this object.
     *
     * @param object $object
     *
     * @return string
     */
    public function getValueType($object): string;

    /**
     * Returns the object ID.
     *
     * @param object $object
     *
     * @return int|string
     */
    public function getId($object);

    /**
     * Returns the object remote ID.
     *
     * @param object $object
     *
     * @return int|string
     */
    public function getRemoteId($object);

    /**
     * Returns the object name.
     *
     * @param object $object
     *
     * @return string
     */
    public function getName($object): string;

    /**
     * Returns if the object is visible.
     *
     * @param object $object
     *
     * @return bool
     */
    public function getIsVisible($object): bool;

    /**
     * Returns the object itself.
     *
     * This method can be used to enrich the object before it being rendered.
     *
     * @param object $object
     *
     * @return object
     */
    public function getObject($object);
}
