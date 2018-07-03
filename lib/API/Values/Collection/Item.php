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
     * Item of this type is inserted between items coming from the collection query.
     */
    public const TYPE_MANUAL = 0;

    /**
     * Items of this type override the item from the query at the specified position.
     */
    public const TYPE_OVERRIDE = 1;

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
     * Returns if the item is a manual item.
     */
    public function isManual(): bool;

    /**
     * Returns if the item is an override item.
     */
    public function isOverride(): bool;

    /**
     * Returns the item definition.
     */
    public function getDefinition(): ItemDefinitionInterface;

    /**
     * Returns the item position within the collection.
     */
    public function getPosition(): int;

    /**
     * Returns the type of this item.
     *
     * Type can either be manual (inserted between items returned from the query),
     * or override (replaces the item from the query in the same position).
     */
    public function getType(): int;

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
