<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Item\ItemInterface;

final class ManualItem implements ItemInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Item
     */
    private $collectionItem;

    public function __construct(CollectionItem $collectionItem)
    {
        $this->collectionItem = $collectionItem;
    }

    /**
     * Returns the collection item that was used to generate this manual item.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getCollectionItem()
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

    public function getValueType()
    {
        return $this->collectionItem->getCmsItem()->getValueType();
    }

    public function getName()
    {
        return $this->collectionItem->getCmsItem()->getName();
    }

    public function isVisible()
    {
        return $this->collectionItem->getCmsItem()->isVisible();
    }

    public function getObject()
    {
        return $this->collectionItem->getCmsItem()->getObject();
    }
}
