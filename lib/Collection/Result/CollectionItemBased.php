<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item as CollectionItemInterface;
use Netgen\BlockManager\Item\ItemInterface;

class CollectionItemBased implements ItemInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemInterface
     */
    protected $innerItem;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Item
     */
    protected $collectionItem;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $innerItem
     * @param \Netgen\BlockManager\API\Values\Collection\Item $collectionItem
     */
    public function __construct(ItemInterface $innerItem, CollectionItemInterface $collectionItem)
    {
        $this->innerItem = $innerItem;
        $this->collectionItem = $collectionItem;
    }

    /**
     * Returns the external value ID.
     *
     * @return int|string
     */
    public function getValueId()
    {
        return $this->innerItem->getValueId();
    }

    /**
     * Returns the external value type.
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->innerItem->getValueType();
    }

    /**
     * Returns the external value name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->innerItem->getName();
    }

    /**
     * Returns if the external value is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->innerItem->isVisible();
    }

    /**
     * Returns the external value object.
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->innerItem->getObject();
    }

    /**
     * Returns the collection item.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getCollectionItem()
    {
        return $this->collectionItem;
    }
}
