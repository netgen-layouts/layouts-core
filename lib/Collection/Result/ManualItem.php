<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\ItemInterface as CmsItem;
use Netgen\BlockManager\Item\NullItem;

final class ManualItem implements ItemInterface
{
    private $cmsItem;

    private $collectionItem;

    public function __construct(CmsItem $cmsItem, CollectionItem $collectionItem)
    {
        $this->cmsItem = $cmsItem;
        $this->collectionItem = $collectionItem;
    }

    public function getCollectionItem()
    {
        return $this->collectionItem;
    }

    public function getInnerItem()
    {
        return $this->cmsItem;
    }

    public function isValid()
    {
        return !$this->cmsItem instanceof NullItem && $this->cmsItem->isVisible();
    }

    public function getValue()
    {
        return $this->cmsItem->getValue();
    }

    public function getRemoteId()
    {
        return $this->cmsItem->getRemoteId();
    }

    public function getValueType()
    {
        return $this->cmsItem->getValueType();
    }

    public function getName()
    {
        return $this->cmsItem->getName();
    }

    public function isVisible()
    {
        return $this->cmsItem->isVisible();
    }

    public function getObject()
    {
        return $this->cmsItem->getObject();
    }
}
