<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\ValueObject;

class Collection extends ValueObject implements APICollection
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    protected $items = array();

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    protected $query;

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the collection status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the collection type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns if the collection is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns all collection items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Returns if the collection has a manual item at specified position.
     *
     * @param int $position
     *
     * @return bool
     */
    public function hasManualItem($position)
    {
        return $this->hasItem(Item::TYPE_MANUAL, $position);
    }

    /**
     * Returns the manual item at specified position.
     *
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getManualItem($position)
    {
        return $this->getItem(Item::TYPE_MANUAL, $position);
    }

    /**
     * Returns the manual items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getManualItems()
    {
        return $this->filterItems(Item::TYPE_MANUAL);
    }

    /**
     * Returns if the collection has an override item at specified position.
     *
     * @param int $position
     *
     * @return bool
     */
    public function hasOverrideItem($position)
    {
        return $this->hasItem(Item::TYPE_OVERRIDE, $position);
    }

    /**
     * Returns the override item at specified position.
     *
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getOverrideItem($position)
    {
        return $this->getItem(Item::TYPE_OVERRIDE, $position);
    }

    /**
     * Returns the override items.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getOverrideItems()
    {
        return $this->filterItems(Item::TYPE_OVERRIDE);
    }

    /**
     * Returns the query from the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns if the item exists at specified position.
     *
     * @param int $type
     * @param int $position
     *
     * @return bool
     */
    protected function hasItem($type, $position)
    {
        foreach ($this->items as $item) {
            if ($item->getType() === $type && $item->getPosition() === $position) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the item at specified position.
     *
     * @param int $type
     * @param int $position
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    protected function getItem($type, $position)
    {
        foreach ($this->items as $item) {
            if ($item->getType() === $type && $item->getPosition() === $position) {
                return $item;
            }
        }
    }

    /**
     * Returns all items of specified type.
     *
     * @param int $type
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    protected function filterItems($type)
    {
        $items = array();

        foreach ($this->items as $item) {
            if ($item->getType() === $type) {
                $items[$item->getPosition()] = $item;
            }
        }

        return $items;
    }
}
