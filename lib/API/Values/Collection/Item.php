<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Item\CmsItemInterface;

interface Item extends Value, ConfigAwareValue
{
    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the ID of the collection to which the item belongs.
     *
     * @return int|string
     */
    public function getCollectionId();

    /**
     * Returns the item definition.
     */
    public function getDefinition(): ItemDefinitionInterface;

    /**
     * Returns the item position within the collection.
     */
    public function getPosition(): int;

    /**
     * Returns the value stored inside the collection item.
     *
     * @return int|string
     */
    public function getValue();

    /**
     * Returns the CMS item loaded from value and value type stored in this collection item.
     */
    public function getCmsItem(): CmsItemInterface;

    /**
     * Returns if the item is valid. An item is valid if the CMS item it wraps is visible and
     * if it actually exists in the CMS.
     */
    public function isValid(): bool;
}
