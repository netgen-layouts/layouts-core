<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Collection implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) UuidInterface $id;

    public private(set) Status $status;

    /**
     * Returns the UUID of the block where this collection located.
     */
    public private(set) UuidInterface $blockId;

    /**
     * Returns the starting collection offset.
     */
    public private(set) int $offset {
        get {
            if (!$this->hasQuery) {
                // Manual collections always use offset of 0
                return 0;
            }

            return $this->offset;
        }
    }

    /**
     * Returns the starting collection limit or null if no limit is set.
     */
    public private(set) ?int $limit;

    /**
     * Returns the query from the collection or null if no query exists.
     */
    public private(set) ?Query $query;

    /**
     * Returns if the query exists in the collection.
     */
    public bool $hasQuery {
        get => $this->query instanceof Query;
    }

    public CollectionType $collectionType {
        get => $this->hasQuery ? CollectionType::Dynamic : CollectionType::Manual;
    }

    public bool $isManual {
        get => $this->collectionType === CollectionType::Manual;
    }

    public bool $isDynamic {
        get => $this->collectionType === CollectionType::Dynamic;
    }

    /**
     * Returns all collection items.
     */
    public private(set) ItemList $items {
        get => ItemList::fromArray($this->items->toArray());
    }

    /**
     * Returns all collection slots.
     *
     * Slots are indexed by their position in the collection.
     */
    public private(set) SlotList $slots {
        get => SlotList::fromArray($this->slots->toArray());
    }

    /**
     * Returns the list of all available locales in the collection.
     *
     * @var string[]
     */
    public private(set) array $availableLocales;

    /**
     * Returns the main locale for the collection.
     */
    public private(set) string $mainLocale;

    /**
     * Returns if the collection is translatable.
     */
    public private(set) bool $isTranslatable;

    /**
     * Returns if the main translation of the collection will be used
     * in case there are no prioritized translations.
     */
    public private(set) bool $alwaysAvailable;

    /**
     * Returns the locale of the currently loaded translation.
     */
    public private(set) string $locale;

    /**
     * Returns if the item exists at specified position.
     */
    public function hasItem(int $position): bool
    {
        return $this->items->exists(
            static fn ($key, Item $item): bool => $item->position === $position,
        );
    }

    /**
     * Returns the item at specified position.
     */
    public function getItem(int $position): ?Item
    {
        foreach ($this->items as $item) {
            if ($item->position === $position) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Returns if the slot exists at specified position.
     */
    public function hasSlot(int $position): bool
    {
        return $this->slots->exists(
            static fn ($key, Slot $slot): bool => $slot->position === $position,
        );
    }

    /**
     * Returns the slot at specified position.
     */
    public function getSlot(int $position): ?Slot
    {
        foreach ($this->slots as $slot) {
            if ($slot->position === $position) {
                return $slot;
            }
        }

        return null;
    }
}
