<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\Config\ConfigAwareValue;
use Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait;
use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

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
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $collectionId;

    /**
     * @var \Netgen\Layouts\Collection\Item\ItemDefinitionInterface
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
     * @var \Netgen\Layouts\Item\CmsItemInterface
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
     * Returns the UUID of the collection to which the item belongs.
     */
    public function getCollectionId(): UuidInterface
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
