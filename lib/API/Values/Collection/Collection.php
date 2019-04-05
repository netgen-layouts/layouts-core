<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\API\Values\LazyPropertyTrait;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\ValueStatusTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Collection implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;
    use LazyPropertyTrait;

    /**
     * Denotes that the collection is manual, i.e., does not have a query.
     */
    public const TYPE_MANUAL = 0;

    /**
     * Denotes that the collection is dynamic, i.e., that it has a query.
     */
    public const TYPE_DYNAMIC = 1;

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
     * Returns the starting collection offset.
     */
    public function getOffset(): int
    {
        if ($this->offset !== null && !$this->hasQuery()) {
            // Manual collections always use offset of 0
            return 0;
        }

        return $this->offset;
    }

    /**
     * Returns the starting collection limit or null if no limit is set.
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Returns if the item exists at specified position.
     */
    public function hasItem(int $position): bool
    {
        return $this->items->exists(
            static function ($key, APIItem $item) use ($position): bool {
                return $item->getPosition() === $position;
            }
        );
    }

    /**
     * Returns the item at specified position.
     */
    public function getItem(int $position): ?APIItem
    {
        foreach ($this->items as $item) {
            if ($item->getPosition() === $position) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Returns all collection items.
     */
    public function getItems(): ItemList
    {
        return new ItemList($this->items->toArray());
    }

    /**
     * Returns the query from the collection or null if no query exists.
     */
    public function getQuery(): ?APIQuery
    {
        return $this->getLazyProperty($this->query);
    }

    /**
     * Returns if the query exists in the collection.
     */
    public function hasQuery(): bool
    {
        return $this->getQuery() instanceof APIQuery;
    }

    /**
     * Returns the list of all available locales in the collection.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    /**
     * Returns the main locale for the collection.
     */
    public function getMainLocale(): string
    {
        return $this->mainLocale;
    }

    /**
     * Returns if the collection is translatable.
     */
    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    /**
     * Returns if the main translation of the collection will be used
     * in case there are no prioritized translations.
     */
    public function isAlwaysAvailable(): bool
    {
        return $this->alwaysAvailable;
    }

    /**
     * Returns the locale of the currently loaded translation.
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
