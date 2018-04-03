<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\NullItem;

final class ManualItem implements ItemInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemInterface
     */
    private $cmsItem;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Item
     */
    private $collectionItem;

    public function __construct(CollectionItem $collectionItem)
    {
        $this->collectionItem = $collectionItem;
        $this->cmsItem = $this->collectionItem->getCmsItem();
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

    /**
     * Returns the CMS item that was used to generate this manual item.
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function getInnerItem()
    {
        return $this->cmsItem;
    }

    /**
     * Returns if the manual item is valid. A manual item is valid if it is visible
     * (both the collection item and CMS item) and if CMS item actually exists in the CMS.
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->cmsItem instanceof NullItem) {
            return false;
        }

        return $this->collectionItem->isVisible() && $this->cmsItem->isVisible();
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
