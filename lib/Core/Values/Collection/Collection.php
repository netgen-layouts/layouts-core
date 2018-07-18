<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Core\Values\ValueStatusTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Collection implements APICollection
{
    use HydratorTrait;
    use ValueStatusTrait;
    use LazyPropertyTrait;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $items;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query|null
     */
    private $query;

    /**
     * @var string[]
     */
    private $availableLocales = [];

    /**
     * @var string
     */
    private $mainLocale;

    /**
     * @var bool
     */
    private $isTranslatable;

    /**
     * @var bool
     */
    private $alwaysAvailable;

    /**
     * @var string
     */
    private $locale;

    public function __construct()
    {
        $this->items = $this->items ?? new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType(): int
    {
        return $this->hasQuery() ? self::TYPE_DYNAMIC : self::TYPE_MANUAL;
    }

    public function getOffset(): int
    {
        if ($this->offset !== null && !$this->hasQuery()) {
            // Manual collections always use offset of 0
            return 0;
        }

        return $this->offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function hasItem(int $position, ?int $type = null): bool
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

    public function getItem(int $position, ?int $type = null): ?APIItem
    {
        foreach ($this->items as $item) {
            if ($item->getPosition() === $position) {
                if ($type === null || $item->getType() === $type) {
                    return $item;
                }

                return null;
            }
        }

        return null;
    }

    public function getItems(): array
    {
        return $this->items->toArray();
    }

    public function hasManualItem(int $position): bool
    {
        return $this->hasItem($position, Item::TYPE_MANUAL);
    }

    public function getManualItem(int $position): ?APIItem
    {
        return $this->getItem($position, Item::TYPE_MANUAL);
    }

    public function getManualItems(): array
    {
        return $this->filterItems(Item::TYPE_MANUAL);
    }

    public function hasOverrideItem(int $position): bool
    {
        return $this->hasItem($position, Item::TYPE_OVERRIDE);
    }

    public function getOverrideItem(int $position): ?APIItem
    {
        return $this->getItem($position, Item::TYPE_OVERRIDE);
    }

    public function getOverrideItems(): array
    {
        return $this->filterItems(Item::TYPE_OVERRIDE);
    }

    public function getQuery(): ?APIQuery
    {
        return $this->getLazyProperty($this->query);
    }

    public function hasQuery(): bool
    {
        return $this->getQuery() instanceof APIQuery;
    }

    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    public function getMainLocale(): string
    {
        return $this->mainLocale;
    }

    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    public function isAlwaysAvailable(): bool
    {
        return $this->alwaysAvailable;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Returns all items of specified type (manual or override).
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    private function filterItems(int $type): array
    {
        return $this->items->filter(
            function (APIItem $item) use ($type): bool {
                return $item->getType() === $type;
            }
        )->toArray();
    }
}
