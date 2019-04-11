<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Item as CollectionItem;
use Netgen\Layouts\Item\CmsItemInterface;

final class ManualItem implements CmsItemInterface
{
    /**
     * @var \Netgen\Layouts\API\Values\Collection\Item
     */
    private $collectionItem;

    public function __construct(CollectionItem $collectionItem)
    {
        $this->collectionItem = $collectionItem;
    }

    /**
     * Returns the collection item that was used to generate this manual item.
     */
    public function getCollectionItem(): Item
    {
        return $this->collectionItem;
    }

    public function getValue()
    {
        return $this->collectionItem->getCmsItem()->getValue();
    }

    public function getRemoteId()
    {
        return $this->collectionItem->getCmsItem()->getRemoteId();
    }

    public function getValueType(): string
    {
        return $this->collectionItem->getCmsItem()->getValueType();
    }

    public function getName(): string
    {
        return $this->collectionItem->getCmsItem()->getName();
    }

    public function isVisible(): bool
    {
        return $this->collectionItem->getCmsItem()->isVisible();
    }

    public function getObject(): ?object
    {
        return $this->collectionItem->getCmsItem()->getObject();
    }
}
