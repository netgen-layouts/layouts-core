<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
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
     * @var string[]
     */
    protected $availableLocales = array();

    /**
     * @var string
     */
    protected $mainLocale;

    /**
     * @var bool
     */
    protected $isTranslatable;

    /**
     * @var bool
     */
    protected $alwaysAvailable;

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function hasManualItem($position)
    {
        return $this->hasItem(Item::TYPE_MANUAL, $position);
    }

    public function getManualItem($position)
    {
        return $this->getItem(Item::TYPE_MANUAL, $position);
    }

    public function getManualItems()
    {
        return $this->filterItems(Item::TYPE_MANUAL);
    }

    public function hasOverrideItem($position)
    {
        return $this->hasItem(Item::TYPE_OVERRIDE, $position);
    }

    public function getOverrideItem($position)
    {
        return $this->getItem(Item::TYPE_OVERRIDE, $position);
    }

    public function getOverrideItems()
    {
        return $this->filterItems(Item::TYPE_OVERRIDE);
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function hasQuery()
    {
        return $this->query instanceof APIQuery;
    }

    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    public function getMainLocale()
    {
        return $this->mainLocale;
    }

    public function hasLocale($locale)
    {
        return in_array($locale, $this->availableLocales, true);
    }

    public function isTranslatable()
    {
        return $this->isTranslatable;
    }

    public function isAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    /**
     * Returns if the item with specified type (manual or override)
     * exists at specified position.
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
     * Returns the item of specified type (manual or override)
     * at specified position.
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
     * Returns all items of specified type (manual or override).
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
