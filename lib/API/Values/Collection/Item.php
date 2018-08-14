<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\API\Values\LazyPropertyTrait;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\ValueStatusTrait;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Item\NullCmsItem;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Item implements Value, ConfigAwareValue
{
    use HydratorTrait;
    use ValueStatusTrait;
    use ConfigAwareValueTrait;
    use LazyPropertyTrait;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int|string
     */
    private $collectionId;

    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    private $definition;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int|string
     */
    private $value;

    /**
     * @var \Netgen\BlockManager\Item\CmsItemInterface
     */
    private $cmsItem;

    /**
     * Returns the item ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the ID of the collection to which the item belongs.
     *
     * @return int|string
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Returns the item definition.
     */
    public function getDefinition(): ItemDefinitionInterface
    {
        return $this->definition;
    }

    /**
     * Returns the item position within the collection.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Returns the value stored inside the collection item.
     *
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the CMS item loaded from value and value type stored in this collection item.
     */
    public function getCmsItem(): CmsItemInterface
    {
        return $this->getLazyProperty($this->cmsItem);
    }

    /**
     * Returns if the item is valid. An item is valid if the CMS item it wraps is visible and
     * if it actually exists in the CMS.
     */
    public function isValid(): bool
    {
        if ($this->getCmsItem() instanceof NullCmsItem) {
            return false;
        }

        return $this->getCmsItem()->isVisible();
    }
}
