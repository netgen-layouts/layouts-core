<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Core\Values\Value;

final class Collection extends Value implements APICollection
{
    use LazyPropertyTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $items;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query|null
     */
    protected $query;

    /**
     * @var string[]
     */
    protected $availableLocales = [];

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

    /**
     * @var string
     */
    protected $locale;

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);

        $this->items = $this->items ?? new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->hasQuery() ? self::TYPE_DYNAMIC : self::TYPE_MANUAL;
    }

    public function getOffset()
    {
        if ($this->offset !== null && !$this->hasQuery()) {
            // Manual collections always use offset of 0
            return 0;
        }

        return $this->offset;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function hasItem($position, $type = null)
    {
        return $this->items->exists(
            function ($key, APIItem $item) use ($position, $type): bool {
                if ($item->getPosition() === $position) {
                    return $type === null || $item->getType() === $type;
                }

                return false;
            }
        );
    }

    public function getItem($position, $type = null)
    {
        foreach ($this->items as $item) {
            if ($item->getPosition() === $position) {
                if ($type === null || $item->getType() === $type) {
                    return $item;
                }

                return;
            }
        }
    }

    public function getItems()
    {
        return $this->items->toArray();
    }

    public function hasManualItem($position)
    {
        return $this->hasItem($position, Item::TYPE_MANUAL);
    }

    public function getManualItem($position)
    {
        return $this->getItem($position, Item::TYPE_MANUAL);
    }

    public function getManualItems()
    {
        return $this->filterItems(Item::TYPE_MANUAL);
    }

    public function hasOverrideItem($position)
    {
        return $this->hasItem($position, Item::TYPE_OVERRIDE);
    }

    public function getOverrideItem($position)
    {
        return $this->getItem($position, Item::TYPE_OVERRIDE);
    }

    public function getOverrideItems()
    {
        return $this->filterItems(Item::TYPE_OVERRIDE);
    }

    public function getQuery()
    {
        return $this->getLazyProperty($this->query);
    }

    public function hasQuery()
    {
        return $this->getQuery() instanceof APIQuery;
    }

    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    public function getMainLocale()
    {
        return $this->mainLocale;
    }

    public function isTranslatable()
    {
        return $this->isTranslatable;
    }

    public function isAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Returns all items of specified type (manual or override).
     *
     * @param int $type
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    private function filterItems($type)
    {
        return $this->items->filter(
            function (APIItem $item) use ($type): bool {
                return $item->getType() === $type;
            }
        )->toArray();
    }
}
