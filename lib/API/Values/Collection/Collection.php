<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Collection implements Value
{
    use HydratorTrait;
    use LazyPropertyTrait;
    use ValueStatusTrait;

    /**
     * Denotes that the collection is manual, i.e., does not have a query.
     */
    public const TYPE_MANUAL = 0;

    /**
     * Denotes that the collection is dynamic, i.e., that it has a query.
     */
    public const TYPE_DYNAMIC = 1;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $blockId;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \Netgen\Layouts\API\Values\Collection\Item>
     */
    private $items;

    /**
     * @var \Netgen\Layouts\API\Values\Collection\Query|null
     */
    private $query;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \Netgen\Layouts\API\Values\Collection\Slot>
     */
    private $slots;

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
        $this->slots = $this->slots ?? new ArrayCollection();
    }

    /**
     * Returns the collection UUID.
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns the UUID of the block where this collection located.
     */
    public function getBlockId(): UuidInterface
    {
        return $this->blockId;
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
            static function ($key, Item $item) use ($position): bool {
                return $item->getPosition() === $position;
            }
        );
    }

    /**
     * Returns the item at specified position.
     */
    public function getItem(int $position): ?Item
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
    public function getQuery(): ?Query
    {
        return $this->getLazyProperty($this->query);
    }

    /**
     * Returns if the query exists in the collection.
     */
    public function hasQuery(): bool
    {
        return $this->getQuery() instanceof Query;
    }

    /**
     * Returns if the slot exists at specified position.
     */
    public function hasSlot(int $position): bool
    {
        return $this->slots->exists(
            static function ($key, Slot $slot) use ($position): bool {
                return $slot->getPosition() === $position;
            }
        );
    }

    /**
     * Returns the slot at specified position.
     */
    public function getSlot(int $position): ?Slot
    {
        foreach ($this->slots as $slot) {
            if ($slot->getPosition() === $position) {
                return $slot;
            }
        }

        return null;
    }

    /**
     * Returns all collection slots.
     *
     * Slots are indexed by their position in the collection.
     */
    public function getSlots(): SlotList
    {
        return new SlotList($this->slots->toArray());
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
