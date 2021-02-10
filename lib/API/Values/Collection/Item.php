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
    use ConfigAwareValueTrait;
    use HydratorTrait;
    use LazyPropertyTrait;
    use ValueStatusTrait;

    private UuidInterface $id;

    private UuidInterface $collectionId;

    private ItemDefinitionInterface $definition;

    private int $position;

    /**
     * @var int|string|null
     */
    private $value;

    private ?string $viewType;

    /**
     * @var \Netgen\Layouts\Item\CmsItemInterface|\Closure
     */
    private $cmsItem;

    public function getId(): UuidInterface
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
     * @return int|string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * View type which will be used to render this item.
     *
     * If null, the view type needs to be set from outside to render the item.
     */
    public function getViewType(): ?string
    {
        return $this->viewType;
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
