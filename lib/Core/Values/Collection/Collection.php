<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\ItemList;
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

    public function hasItem(int $position): bool
    {
        return $this->items->exists(
            function ($key, APIItem $item) use ($position): bool {
                return $item->getPosition() === $position;
            }
        );
    }

    public function getItem(int $position): ?APIItem
    {
        foreach ($this->items as $item) {
            if ($item->getPosition() === $position) {
                return $item;
            }
        }

        return null;
    }

    public function getItems(): ItemList
    {
        return new ItemList($this->items->toArray());
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
}
