<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\Layouts\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Block\CollectionReference;
use Netgen\Layouts\Persistence\Values\Collection\Collection;
use Netgen\Layouts\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Item;
use Netgen\Layouts\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\ItemUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Query;
use Netgen\Layouts\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\QueryTranslationUpdateStruct;
use Netgen\Layouts\Persistence\Values\Collection\Slot;
use Netgen\Layouts\Persistence\Values\Collection\SlotCreateStruct;
use Netgen\Layouts\Persistence\Values\Collection\SlotUpdateStruct;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function array_fill_keys;
use function array_values;
use function count;
use function in_array;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;
use function sprintf;

final class CollectionHandler implements CollectionHandlerInterface
{
    private CollectionQueryHandler $queryHandler;

    private CollectionMapper $collectionMapper;

    private PositionHelper $positionHelper;

    public function __construct(
        CollectionQueryHandler $queryHandler,
        CollectionMapper $collectionMapper,
        PositionHelper $positionHelper
    ) {
        $this->queryHandler = $queryHandler;
        $this->collectionMapper = $collectionMapper;
        $this->positionHelper = $positionHelper;
    }

    public function loadCollection($collectionId, int $status): Collection
    {
        $collectionId = $collectionId instanceof UuidInterface ? $collectionId->toString() : $collectionId;
        $data = $this->queryHandler->loadCollectionData($collectionId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('collection', $collectionId);
        }

        return $this->collectionMapper->mapCollections($data)[0];
    }

    public function loadCollections(Block $block): array
    {
        $data = $this->queryHandler->loadBlockCollectionsData($block);
        $collections = $this->collectionMapper->mapCollections($data, $block->uuid);

        // We need the collection identifier as a hash key, so we'll take it
        // from the loaded data directly.
        $collectionsWithIdentifier = [];

        foreach ($collections as $collection) {
            foreach ($data as $dataItem) {
                if ($collection->uuid === $dataItem['uuid']) {
                    $collectionsWithIdentifier[(string) $dataItem['identifier']] = $collection;

                    break;
                }
            }
        }

        return $collectionsWithIdentifier;
    }

    public function loadCollectionReference(Block $block, string $identifier): CollectionReference
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block, $identifier);

        if (count($data) === 0) {
            throw new NotFoundException('collection reference', $identifier);
        }

        return $this->collectionMapper->mapCollectionReferences($data)[0];
    }

    public function loadCollectionReferences(Block $block): array
    {
        $data = $this->queryHandler->loadCollectionReferencesData($block);

        return $this->collectionMapper->mapCollectionReferences($data);
    }

    public function loadItem($itemId, int $status): Item
    {
        $itemId = $itemId instanceof UuidInterface ? $itemId->toString() : $itemId;
        $data = $this->queryHandler->loadItemData($itemId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('item', $itemId);
        }

        return $this->collectionMapper->mapItems($data)[0];
    }

    public function loadItemWithPosition(Collection $collection, int $position): Item
    {
        $data = $this->queryHandler->loadItemWithPositionData($collection, $position);

        if (count($data) === 0) {
            throw new NotFoundException(
                sprintf(
                    'item in collection with ID "%s" at position %d',
                    $collection->id,
                    $position,
                ),
            );
        }

        return $this->collectionMapper->mapItems($data)[0];
    }

    public function loadCollectionItems(Collection $collection): array
    {
        return $this->collectionMapper->mapItems(
            $this->queryHandler->loadCollectionItemsData($collection),
        );
    }

    public function loadQuery($queryId, int $status): Query
    {
        $queryId = $queryId instanceof UuidInterface ? $queryId->toString() : $queryId;
        $data = $this->queryHandler->loadQueryData($queryId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('query', $queryId);
        }

        $query = $this->collectionMapper->mapQueries($data)[0];

        $collection = $this->loadCollection($query->collectionId, $query->status);

        $query->isTranslatable = $collection->isTranslatable;
        $query->mainLocale = $collection->mainLocale;
        $query->alwaysAvailable = $collection->alwaysAvailable;

        return $query;
    }

    public function loadCollectionQuery(Collection $collection): Query
    {
        $data = $this->queryHandler->loadCollectionQueryData($collection);

        if (count($data) === 0) {
            throw new NotFoundException('query for collection', $collection->id);
        }

        $query = $this->collectionMapper->mapQueries($data)[0];

        $query->isTranslatable = $collection->isTranslatable;
        $query->mainLocale = $collection->mainLocale;
        $query->alwaysAvailable = $collection->alwaysAvailable;

        return $query;
    }

    public function loadSlot($slotId, int $status): Slot
    {
        $slotId = $slotId instanceof UuidInterface ? $slotId->toString() : $slotId;
        $data = $this->queryHandler->loadSlotData($slotId, $status);

        if (count($data) === 0) {
            throw new NotFoundException('slot', $slotId);
        }

        $mappedSlots = $this->collectionMapper->mapSlots($data);

        return array_values($mappedSlots)[0];
    }

    public function loadCollectionSlots(Collection $collection): array
    {
        return $this->collectionMapper->mapSlots(
            $this->queryHandler->loadCollectionSlotsData($collection),
        );
    }

    public function collectionExists($collectionId, int $status): bool
    {
        $collectionId = $collectionId instanceof UuidInterface ? $collectionId->toString() : $collectionId;

        return $this->queryHandler->collectionExists($collectionId, $status);
    }

    public function createCollection(CollectionCreateStruct $collectionCreateStruct, Block $block, string $collectionIdentifier): Collection
    {
        if ($collectionCreateStruct->status !== $block->status) {
            throw new BadStateException('block', 'Collections can only be created in blocks with the same status.');
        }

        $newCollection = Collection::fromArray(
            [
                'uuid' => Uuid::uuid4()->toString(),
                'blockId' => $block->id,
                'blockUuid' => $block->uuid,
                'status' => $collectionCreateStruct->status,
                'offset' => $collectionCreateStruct->offset,
                'limit' => $collectionCreateStruct->limit,
                'isTranslatable' => $collectionCreateStruct->isTranslatable,
                'alwaysAvailable' => $collectionCreateStruct->alwaysAvailable,
                'mainLocale' => $collectionCreateStruct->mainLocale,
                'availableLocales' => [$collectionCreateStruct->mainLocale],
            ],
        );

        $newCollection = $this->queryHandler->createCollection($newCollection);

        $this->queryHandler->createCollectionTranslation(
            $newCollection,
            $collectionCreateStruct->mainLocale,
        );

        $newCollectionReference = CollectionReference::fromArray(
            [
                'blockId' => $block->id,
                'blockStatus' => $block->status,
                'collectionId' => $newCollection->id,
                'collectionStatus' => $newCollection->status,
                'identifier' => $collectionIdentifier,
            ],
        );

        $this->queryHandler->createCollectionReference($newCollectionReference);

        // Reload the collection to get the new block reference data
        return $this->loadCollection($newCollection->id, $newCollection->status);
    }

    public function createCollectionTranslation(Collection $collection, string $locale, string $sourceLocale): Collection
    {
        if (in_array($locale, $collection->availableLocales, true)) {
            throw new BadStateException('locale', 'Collection already has the provided locale.');
        }

        if (!in_array($sourceLocale, $collection->availableLocales, true)) {
            throw new BadStateException('locale', 'Collection does not have the provided source locale.');
        }

        $updatedCollection = clone $collection;
        $updatedCollection->availableLocales[] = $locale;

        $this->queryHandler->createCollectionTranslation($collection, $locale);

        $query = null;

        try {
            $query = $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        if ($query instanceof Query) {
            $updatedQuery = clone $query;
            $updatedQuery->availableLocales[] = $locale;
            $updatedQuery->parameters[$locale] = $updatedQuery->parameters[$sourceLocale];

            $this->queryHandler->createQueryTranslation($updatedQuery, $locale);
        }

        return $updatedCollection;
    }

    /**
     * Adds the provided collection to the block and assigns it the specified identifier.
     */
    public function createCollectionReference(Collection $collection, Block $block, string $collectionIdentifier): CollectionReference
    {
        $newCollectionReference = CollectionReference::fromArray(
            [
                'blockId' => $block->id,
                'blockStatus' => $block->status,
                'collectionId' => $collection->id,
                'collectionStatus' => $collection->status,
                'identifier' => $collectionIdentifier,
            ],
        );

        $this->queryHandler->createCollectionReference($newCollectionReference);

        return $newCollectionReference;
    }

    public function setMainTranslation(Collection $collection, string $mainLocale): Collection
    {
        if (!in_array($mainLocale, $collection->availableLocales, true)) {
            throw new BadStateException('mainLocale', 'Collection does not have the provided locale.');
        }

        $updatedCollection = clone $collection;
        $updatedCollection->mainLocale = $mainLocale;

        $this->queryHandler->updateCollection($updatedCollection);

        return $updatedCollection;
    }

    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct): Collection
    {
        $updatedCollection = clone $collection;

        if (is_int($collectionUpdateStruct->offset)) {
            $updatedCollection->offset = $collectionUpdateStruct->offset;
        }

        if (is_int($collectionUpdateStruct->limit)) {
            // Limit can be 0 to indicate that we want to disable the limit
            $updatedCollection->limit = $collectionUpdateStruct->limit !== 0 ?
                $collectionUpdateStruct->limit :
                null;
        }

        if (is_bool($collectionUpdateStruct->isTranslatable)) {
            $updatedCollection->isTranslatable = $collectionUpdateStruct->isTranslatable;
        }

        if (is_bool($collectionUpdateStruct->alwaysAvailable)) {
            $updatedCollection->alwaysAvailable = $collectionUpdateStruct->alwaysAvailable;
        }

        $this->queryHandler->updateCollection($updatedCollection);

        return $updatedCollection;
    }

    public function copyCollection(Collection $collection, Block $block, string $collectionIdentifier): Collection
    {
        if ($collection->status !== $block->status) {
            throw new BadStateException('block', 'Collections can only be copied to blocks with the same status.');
        }

        $newCollection = clone $collection;

        unset($newCollection->id);
        $newCollection->uuid = Uuid::uuid4()->toString();

        $newCollection = $this->queryHandler->createCollection($newCollection);

        foreach ($newCollection->availableLocales as $locale) {
            $this->queryHandler->createCollectionTranslation($newCollection, $locale);
        }

        $collectionItems = $this->loadCollectionItems($collection);

        foreach ($collectionItems as $collectionItem) {
            $newItem = clone $collectionItem;

            unset($newItem->id);
            $newItem->uuid = Uuid::uuid4()->toString();

            $newItem->collectionId = $newCollection->id;
            $newItem->collectionUuid = $newCollection->uuid;

            $this->queryHandler->addItem($newItem);
        }

        $collectionQuery = null;

        try {
            $collectionQuery = $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        if ($collectionQuery instanceof Query) {
            $newQuery = clone $collectionQuery;

            unset($newQuery->id);
            $newQuery->uuid = Uuid::uuid4()->toString();

            $newQuery->collectionId = $newCollection->id;
            $newQuery->collectionUuid = $newCollection->uuid;

            $this->queryHandler->createQuery($newQuery);

            foreach ($newQuery->availableLocales as $locale) {
                $this->queryHandler->createQueryTranslation($newQuery, $locale);
            }
        }

        $collectionSlots = $this->loadCollectionSlots($collection);

        foreach ($collectionSlots as $collectionSlot) {
            $newSlot = clone $collectionSlot;

            unset($newSlot->id);
            $newSlot->uuid = Uuid::uuid4()->toString();

            $newSlot->collectionId = $newCollection->id;
            $newSlot->collectionUuid = $newCollection->uuid;

            $this->queryHandler->addSlot($newSlot);
        }

        $newCollectionReference = CollectionReference::fromArray(
            [
                'blockId' => $block->id,
                'blockStatus' => $block->status,
                'collectionId' => $newCollection->id,
                'collectionStatus' => $newCollection->status,
                'identifier' => $collectionIdentifier,
            ],
        );

        $this->queryHandler->createCollectionReference($newCollectionReference);

        // Reload the collection to get the new block reference data
        return $this->loadCollection($newCollection->id, $newCollection->status);
    }

    public function createCollectionStatus(Collection $collection, int $newStatus): Collection
    {
        $newCollection = clone $collection;
        $newCollection->status = $newStatus;

        $this->queryHandler->createCollection($newCollection);

        foreach ($newCollection->availableLocales as $locale) {
            $this->queryHandler->createCollectionTranslation($newCollection, $locale);
        }

        $collectionItems = $this->loadCollectionItems($collection);

        foreach ($collectionItems as $collectionItem) {
            $newItem = clone $collectionItem;
            $newItem->status = $newStatus;

            $this->queryHandler->addItem($newItem);
        }

        $collectionQuery = null;

        try {
            $collectionQuery = $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        if ($collectionQuery instanceof Query) {
            $newQuery = clone $collectionQuery;
            $newQuery->status = $newStatus;

            $this->queryHandler->createQuery($newQuery);

            foreach ($newQuery->availableLocales as $locale) {
                $this->queryHandler->createQueryTranslation($newQuery, $locale);
            }
        }

        $collectionSlots = $this->loadCollectionSlots($collection);

        foreach ($collectionSlots as $collectionSlot) {
            $newSlot = clone $collectionSlot;
            $newSlot->status = $newStatus;

            $this->queryHandler->addSlot($newSlot);
        }

        return $newCollection;
    }

    public function deleteCollection(int $collectionId, ?int $status = null): void
    {
        $this->queryHandler->deleteCollectionSlots($collectionId, $status);
        $this->queryHandler->deleteCollectionItems($collectionId, $status);

        $queryIds = $this->queryHandler->loadCollectionQueryIds($collectionId, $status);
        $this->queryHandler->deleteQueryTranslations($queryIds, $status);
        $this->queryHandler->deleteQuery($queryIds, $status);

        $this->queryHandler->deleteCollectionTranslations($collectionId, $status);
        $this->queryHandler->deleteCollection($collectionId, $status);
    }

    public function deleteCollectionTranslation(Collection $collection, string $locale): Collection
    {
        if (!in_array($locale, $collection->availableLocales, true)) {
            throw new BadStateException('locale', 'Collection does not have the provided locale.');
        }

        if ($locale === $collection->mainLocale) {
            throw new BadStateException('locale', 'Main translation cannot be removed from the collection.');
        }

        $queryIds = $this->queryHandler->loadCollectionQueryIds($collection->id, $collection->status);
        $this->queryHandler->deleteQueryTranslations($queryIds, $collection->status, $locale);

        $this->queryHandler->deleteCollectionTranslations($collection->id, $collection->status, $locale);

        return $this->loadCollection($collection->id, $collection->status);
    }

    public function deleteBlockCollections(array $blockIds, ?int $status = null): void
    {
        $collectionIds = $this->queryHandler->loadBlockCollectionIds($blockIds, $status);
        foreach ($collectionIds as $collectionId) {
            $this->deleteCollection($collectionId, $status);
        }

        $this->queryHandler->deleteCollectionReferences($blockIds, $status);
    }

    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct): Item
    {
        $position = $this->createItemPosition($collection, $itemCreateStruct->position);

        $newItem = Item::fromArray(
            [
                'uuid' => Uuid::uuid4()->toString(),
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'position' => $position,
                'value' => $itemCreateStruct->value,
                'valueType' => $itemCreateStruct->valueType,
                'viewType' => $itemCreateStruct->viewType,
                'status' => $collection->status,
                'config' => $itemCreateStruct->config,
            ],
        );

        return $this->queryHandler->addItem($newItem);
    }

    public function updateItem(Item $item, ItemUpdateStruct $itemUpdateStruct): Item
    {
        $updatedItem = clone $item;

        if (is_string($itemUpdateStruct->viewType)) {
            $updatedItem->viewType = $itemUpdateStruct->viewType !== '' ? $itemUpdateStruct->viewType : null;
        }

        if (is_array($itemUpdateStruct->config)) {
            $updatedItem->config = $itemUpdateStruct->config;
        }

        $this->queryHandler->updateItem($updatedItem);

        return $updatedItem;
    }

    public function moveItem(Item $item, int $position): Item
    {
        $collection = $this->loadCollection($item->collectionId, $item->status);

        $switchWithItem = null;
        if ($item->position + 1 === $position || $item->position - 1 === $position) {
            try {
                $switchWithItem = $this->loadItemWithPosition($collection, $position);
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        if ($switchWithItem instanceof Item) {
            // If we're moving an item to an adjacent position where an item is already
            // present, we simply switch their positions in order to minimize impact on
            // other items
            $this->switchItemPositions($item, $switchWithItem);

            return $this->loadItem($item->id, $item->status);
        }

        $movedItem = clone $item;

        if ($item->position !== $position) {
            $movedItem->position = $this->moveItemToPosition($collection, $item, $position);

            $this->queryHandler->updateItem($movedItem);
        }

        return $movedItem;
    }

    public function switchItemPositions(Item $item1, Item $item2): void
    {
        if ($item1->id === $item2->id) {
            throw new BadStateException('item1', 'First and second items are the same.');
        }

        if ($item1->collectionId !== $item2->collectionId) {
            throw new BadStateException('item1', 'Positions can be switched only for items within the same collection.');
        }

        $updatedItem1 = clone $item1;
        $updatedItem2 = clone $item2;

        $updatedItem1->position = $item2->position;
        $updatedItem2->position = $item1->position;

        $this->queryHandler->updateItem($updatedItem1);
        $this->queryHandler->updateItem($updatedItem2);
    }

    public function deleteItem(Item $item): void
    {
        $collection = $this->loadCollection($item->collectionId, $item->status);

        $this->queryHandler->deleteItem($item->id, $item->status);

        if (!$this->isCollectionDynamic($collection)) {
            $this->positionHelper->removePosition(
                $this->getPositionHelperItemConditions(
                    $item->collectionId,
                    $item->status,
                ),
                $item->position,
            );
        }
    }

    public function deleteItems(Collection $collection): Collection
    {
        $this->queryHandler->deleteCollectionItems($collection->id, $collection->status);

        return $this->loadCollection($collection->id, $collection->status);
    }

    public function slotWithPositionExists(Collection $collection, int $position): bool
    {
        return $this->queryHandler->slotWithPositionExists($collection, $position);
    }

    public function addSlot(Collection $collection, SlotCreateStruct $slotCreateStruct): Slot
    {
        if ($this->slotWithPositionExists($collection, $slotCreateStruct->position)) {
            throw new BadStateException('position', sprintf('Slot with provided position already exists in the collection with ID %d', $collection->id));
        }

        $newSlot = Slot::fromArray(
            [
                'uuid' => Uuid::uuid4()->toString(),
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'position' => $slotCreateStruct->position,
                'viewType' => $slotCreateStruct->viewType,
                'status' => $collection->status,
            ],
        );

        return $this->queryHandler->addSlot($newSlot);
    }

    public function updateSlot(Slot $slot, SlotUpdateStruct $slotUpdateStruct): Slot
    {
        $updatedSlot = clone $slot;

        if (is_string($slotUpdateStruct->viewType)) {
            $updatedSlot->viewType = $slotUpdateStruct->viewType !== '' ? $slotUpdateStruct->viewType : null;
        }

        $this->queryHandler->updateSlot($updatedSlot);

        return $updatedSlot;
    }

    public function deleteSlot(Slot $slot): void
    {
        $this->queryHandler->deleteSlot($slot->id, $slot->status);
    }

    public function deleteSlots(Collection $collection): Collection
    {
        $this->queryHandler->deleteCollectionSlots($collection->id, $collection->status);

        return $this->loadCollection($collection->id, $collection->status);
    }

    public function createQuery(Collection $collection, QueryCreateStruct $queryCreateStruct): Query
    {
        try {
            $this->loadCollectionQuery($collection);

            throw new BadStateException('collection', 'Provided collection already has a query.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $newQuery = Query::fromArray(
            [
                'uuid' => Uuid::uuid4()->toString(),
                'collectionId' => $collection->id,
                'collectionUuid' => $collection->uuid,
                'type' => $queryCreateStruct->type,
                'status' => $collection->status,
                'isTranslatable' => $collection->isTranslatable,
                'alwaysAvailable' => $collection->alwaysAvailable,
                'mainLocale' => $collection->mainLocale,
                'availableLocales' => $collection->availableLocales,
                'parameters' => array_fill_keys(
                    $collection->availableLocales,
                    $queryCreateStruct->parameters,
                ),
            ],
        );

        $newQuery = $this->queryHandler->createQuery($newQuery);

        foreach ($collection->availableLocales as $collectionLocale) {
            $this->queryHandler->createQueryTranslation($newQuery, $collectionLocale);
        }

        return $newQuery;
    }

    public function updateQueryTranslation(Query $query, string $locale, QueryTranslationUpdateStruct $translationUpdateStruct): Query
    {
        $updatedQuery = clone $query;

        if (!in_array($locale, $query->availableLocales, true)) {
            throw new BadStateException('locale', 'Query does not have the provided locale.');
        }

        if (is_array($translationUpdateStruct->parameters)) {
            $updatedQuery->parameters[$locale] = $translationUpdateStruct->parameters;
        }

        $this->queryHandler->updateQueryTranslation($updatedQuery, $locale);

        return $updatedQuery;
    }

    public function deleteCollectionQuery(Collection $collection): void
    {
        $queryIds = $this->queryHandler->loadCollectionQueryIds($collection->id, $collection->status);
        $this->queryHandler->deleteQueryTranslations($queryIds, $collection->status);
        $this->queryHandler->deleteQuery($queryIds, $collection->status);
    }

    /**
     * Returns if the provided collection is a dynamic collection (i.e. if it has a query).
     */
    private function isCollectionDynamic(Collection $collection): bool
    {
        try {
            $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * Creates space for a new item by shifting positions of other items below the new position.
     *
     * In case of a manual collection, the case is simple, all items below the position
     * are incremented.
     *
     * In case of a dynamic collection, the items below are incremented, but only up
     * until the first break in positions.
     */
    private function createItemPosition(Collection $collection, ?int $newPosition): int
    {
        if (!$this->isCollectionDynamic($collection)) {
            return $this->positionHelper->createPosition(
                $this->getPositionHelperItemConditions(
                    $collection->id,
                    $collection->status,
                ),
                $newPosition,
            );
        }

        if ($newPosition === null) {
            throw new BadStateException('collection', 'When adding items to dynamic collections, position is mandatory.');
        }

        return $this->incrementItemPositions($collection, $newPosition);
    }

    /**
     * Moves the item to provided position.
     *
     * In case of a manual collection, the case is simple, only positions between the old
     * and new position are ever updated.
     *
     * In case of a dynamic collection, the items below the new position are incremented,
     * but only up until the first break in positions. The positions are never decremented.
     */
    private function moveItemToPosition(Collection $collection, Item $item, int $newPosition): int
    {
        if (!$this->isCollectionDynamic($collection)) {
            return $this->positionHelper->moveToPosition(
                $this->getPositionHelperItemConditions(
                    $collection->id,
                    $collection->status,
                ),
                $item->position,
                $newPosition,
            );
        }

        return $this->incrementItemPositions($collection, $newPosition, $item->position);
    }

    /**
     * Creates space for a new item by shifting positions of other items
     * below the new position, but only up until the first break in positions,
     * or up to $maxPosition if provided.
     */
    private function incrementItemPositions(Collection $collection, int $startPosition, ?int $maxPosition = null): int
    {
        $items = $this->loadCollectionItems($collection);
        $endPosition = $startPosition - 1;

        foreach ($items as $item) {
            if ($item->position < $startPosition) {
                // Skip all items located before the start position,
                // we don't need to touch those.
                continue;
            }

            if ($item->position - $endPosition > 1 || $item->position === $maxPosition) {
                // Once we reach a break in positions, or if we we've come to the maximum
                // allowed position, we simply stop.
                break;
            }

            $endPosition = $item->position;
        }

        if ($endPosition < $startPosition) {
            return $startPosition;
        }

        return $this->positionHelper->createPosition(
            $this->getPositionHelperItemConditions(
                $collection->id,
                $collection->status,
            ),
            $startPosition,
            $endPosition,
            true,
        );
    }

    /**
     * Builds the condition array that will be used with position helper and items in collections.
     *
     * @return array<string, mixed>
     */
    private function getPositionHelperItemConditions(int $collectionId, int $status): array
    {
        return [
            'table' => 'nglayouts_collection_item',
            'column' => 'position',
            'conditions' => [
                'collection_id' => $collectionId,
                'status' => $status,
            ],
        ];
    }
}
