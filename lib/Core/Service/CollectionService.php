<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct as APIQueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct as APIQueryUpdateStruct;
use Netgen\BlockManager\Core\Values\QueryCreateStruct;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use Exception;

class CollectionService implements APICollectionService
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    protected $collectionValidator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $collectionValidator
     * @param \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper $collectionMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(
        CollectionValidator $collectionValidator,
        CollectionMapper $collectionMapper,
        Handler $persistenceHandler
    ) {
        $this->collectionValidator = $collectionValidator;
        $this->collectionMapper = $collectionMapper;
        $this->persistenceHandler = $persistenceHandler;

        $this->collectionHandler = $persistenceHandler->getCollectionHandler();
    }

    /**
     * Loads a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollection($collectionId, $status = Collection::STATUS_PUBLISHED)
    {
        $this->collectionValidator->validateId($collectionId, 'collectionId');

        return $this->collectionMapper->mapCollection(
            $this->collectionHandler->loadCollection(
                $collectionId,
                $status
            )
        );
    }

    /**
     * Loads all named collections.
     *
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    public function loadNamedCollections($status = Collection::STATUS_PUBLISHED)
    {
        $persistenceCollections = $this->collectionHandler->loadNamedCollections($status);

        $collections = array();
        foreach ($persistenceCollections as $persistenceCollection) {
            $collections[] = $this->collectionMapper->mapCollection($persistenceCollection);
        }

        return $collections;
    }

    /**
     * Loads an item with specified ID.
     *
     * @param int|string $itemId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItem($itemId, $status = Collection::STATUS_PUBLISHED)
    {
        $this->collectionValidator->validateId($itemId, 'itemId');

        return $this->collectionMapper->mapItem(
            $this->collectionHandler->loadItem(
                $itemId,
                $status
            )
        );
    }

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQuery($queryId, $status = Collection::STATUS_PUBLISHED)
    {
        $this->collectionValidator->validateId($queryId, 'queryId');

        return $this->collectionMapper->mapQuery(
            $this->collectionHandler->loadQuery(
                $queryId,
                $status
            )
        );
    }

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection with provided name already exists (If creating a named collection)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct)
    {
        $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);

        if ($collectionCreateStruct->type === Collection::TYPE_NAMED) {
            if ($this->collectionHandler->namedCollectionExists($collectionCreateStruct->name)) {
                throw new BadStateException('name', 'Named collection with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdCollection = $this->collectionHandler->createCollection(
                $collectionCreateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($createdCollection);
    }

    /**
     * Updates a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not named
     *                                                              If collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function updateNamedCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        $this->collectionValidator->validateCollectionUpdateStruct($collectionUpdateStruct);

        if ($persistenceCollection->type !== Collection::TYPE_NAMED) {
            throw new BadStateException('collection', 'Only named collections can be updated.');
        }

        if ($collectionUpdateStruct->name !== null) {
            if ($this->collectionHandler->namedCollectionExists($collectionUpdateStruct->name)) {
                throw new BadStateException('name', 'Named collection with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedCollection = $this->collectionHandler->updateNamedCollection(
                $persistenceCollection->id,
                $persistenceCollection->status,
                $collectionUpdateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($updatedCollection);
    }

    /**
     * Copies a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedCollectionId = $this->collectionHandler->copyCollection(
                $persistenceCollection->id
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->loadCollection($copiedCollectionId, $persistenceCollection->status);
    }

    /**
     * Creates a new collection status.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection already has the provided status
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollectionStatus(Collection $collection, $status)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        if ($this->collectionHandler->collectionExists($persistenceCollection->id, $status)) {
            throw new BadStateException('status', 'Collection already has the provided status.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdCollection = $this->collectionHandler->createCollectionStatus(
                $persistenceCollection->id,
                $persistenceCollection->status,
                $status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($createdCollection);
    }

    /**
     * Creates a collection draft.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not published
     *                                                              If draft already exists for collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createDraft(Collection $collection)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        if ($persistenceCollection->status !== Collection::STATUS_PUBLISHED) {
            throw new BadStateException('collection', 'Drafts can be created only from published collections.');
        }

        if ($this->collectionHandler->collectionExists($persistenceCollection->id, Collection::STATUS_DRAFT)) {
            throw new BadStateException('collection', 'The provided collection already has a draft.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_DRAFT);
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_TEMPORARY_DRAFT);
            $collectionDraft = $this->collectionHandler->createCollectionStatus($persistenceCollection->id, Collection::STATUS_PUBLISHED, Collection::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($collectionDraft);
    }

    /**
     * Publishes a collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function publishCollection(Collection $collection)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        if ($persistenceCollection->status !== Collection::STATUS_DRAFT) {
            throw new BadStateException('collection', 'Only collections in draft status can be published.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_ARCHIVED);

            $this->collectionHandler->createCollectionStatus($persistenceCollection->id, Collection::STATUS_PUBLISHED, Collection::STATUS_ARCHIVED);
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_PUBLISHED);

            $publishedCollection = $this->collectionHandler->createCollectionStatus($persistenceCollection->id, Collection::STATUS_DRAFT, Collection::STATUS_PUBLISHED);
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_DRAFT);
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_TEMPORARY_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($publishedCollection);
    }

    /**
     * Deletes a specified collection.
     *
     * If $deleteAllStatuses is set to true, collection is completely deleted.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param bool $deleteAllStatuses
     */
    public function deleteCollection(Collection $collection, $deleteAllStatuses = false)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection(
                $persistenceCollection->id,
                $deleteAllStatuses ? null : $persistenceCollection->status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Adds an item to collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If override item is added to manual collection
     *                                                              If item already exists in provided position (only for non manual collections)
     *                                                              If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct, $position = null)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        $this->collectionValidator->validatePosition(
            $position,
            'position',
            $persistenceCollection->type !== Collection::TYPE_MANUAL
        );

        $this->collectionValidator->validateItemCreateStruct($itemCreateStruct);

        if ($persistenceCollection->type === Collection::TYPE_MANUAL) {
            if ($itemCreateStruct->type === Item::TYPE_OVERRIDE) {
                throw new BadStateException('type', 'Override item cannot be added to manual collection.');
            }
        } else {
            if ($this->collectionHandler->itemPositionExists($persistenceCollection->id, $persistenceCollection->status, $position)) {
                throw new BadStateException('position', 'Item already exists on that position.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdItem = $this->collectionHandler->addItem(
                $persistenceCollection->id,
                $persistenceCollection->status,
                $itemCreateStruct,
                $position
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapItem($createdItem);
    }

    /**
     * Moves an item within the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If item already exists in provided position (only for non manual collections)
     *                                                              If position is out of range (for manual collections)
     */
    public function moveItem(Item $item, $position)
    {
        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), $item->getStatus());

        $this->collectionValidator->validatePosition($position, 'position', true);

        $collection = $this->collectionHandler->loadCollection(
            $persistenceItem->collectionId,
            $persistenceItem->status
        );

        if ($collection->type !== Collection::TYPE_MANUAL) {
            if ($this->collectionHandler->itemPositionExists($collection->id, $collection->status, $position)) {
                throw new BadStateException('position', 'Item already exists on that position.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $movedItem = $this->collectionHandler->moveItem(
                $persistenceItem->id,
                $persistenceItem->status,
                $position
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapItem($movedItem);
    }

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     */
    public function deleteItem(Item $item)
    {
        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), $item->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteItem(
                $persistenceItem->id,
                $persistenceItem->status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Adds a query to collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is added to manual collection
     *                                                              If query with specified identifier already exists within the collection
     *                                                              If position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function addQuery(Collection $collection, APIQueryCreateStruct $queryCreateStruct, $position = null)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        if ($persistenceCollection->type === Collection::TYPE_MANUAL) {
            throw new BadStateException('queryCreateStruct', 'Query cannot be added to manual collection.');
        }

        $this->collectionValidator->validatePosition($position, 'position');

        $this->collectionValidator->validateQueryCreateStruct($queryCreateStruct);

        if ($this->collectionHandler->queryIdentifierExists($persistenceCollection->id, $persistenceCollection->status, $queryCreateStruct->identifier)) {
            throw new BadStateException('identifier', 'Query with specified identifier already exists.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdQuery = $this->collectionHandler->addQuery(
                $persistenceCollection->id,
                $persistenceCollection->status,
                $queryCreateStruct,
                $position
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapQuery($createdQuery);
    }

    /**
     * Updates a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query with specified identifier already exists within the collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function updateQuery(Query $query, APIQueryUpdateStruct $queryUpdateStruct)
    {
        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), $query->getStatus());

        $this->collectionValidator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        if ($queryUpdateStruct->identifier !== null && $queryUpdateStruct->identifier !== $persistenceQuery->identifier) {
            if ($this->collectionHandler->queryIdentifierExists($persistenceQuery->collectionId, $persistenceQuery->status, $queryUpdateStruct->identifier)) {
                throw new BadStateException('identifier', 'Query with specified identifier already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedQuery = $this->collectionHandler->updateQuery(
                $persistenceQuery->id,
                $persistenceQuery->status,
                $queryUpdateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapQuery($updatedQuery);
    }

    /**
     * Moves a query within the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If position is out of range
     */
    public function moveQuery(Query $query, $position)
    {
        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), $query->getStatus());

        $this->collectionValidator->validatePosition($position, 'position', true);

        $this->persistenceHandler->beginTransaction();

        try {
            $movedQuery = $this->collectionHandler->moveQuery(
                $persistenceQuery->id,
                $persistenceQuery->status,
                $position
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapQuery($movedQuery);
    }

    /**
     * Removes a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     */
    public function deleteQuery(Query $query)
    {
        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), $query->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteQuery(
                $persistenceQuery->id,
                $persistenceQuery->status
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Creates a new collection create struct.
     *
     * @param string $type
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\CollectionCreateStruct
     */
    public function newCollectionCreateStruct($type, $name = null)
    {
        return new CollectionCreateStruct(
            array(
                'type' => $type,
                'name' => $name,
            )
        );
    }

    /**
     * Creates a new collection update struct.
     *
     * @return \Netgen\BlockManager\API\Values\CollectionUpdateStruct
     */
    public function newCollectionUpdateStruct()
    {
        return new CollectionUpdateStruct();
    }

    /**
     * Creates a new item create struct.
     *
     * @param int $type
     * @param int|string $valueId
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\API\Values\ItemCreateStruct
     */
    public function newItemCreateStruct($type, $valueId, $valueType)
    {
        return new ItemCreateStruct(
            array(
                'type' => $type,
                'valueId' => $valueId,
                'valueType' => $valueType,
            )
        );
    }

    /**
     * Creates a new query create struct.
     *
     * @param string $identifier
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\QueryCreateStruct
     */
    public function newQueryCreateStruct($identifier, $type)
    {
        return new QueryCreateStruct(
            array(
                'identifier' => $identifier,
                'type' => $type,
            )
        );
    }

    /**
     * Creates a new query update struct.
     *
     * @return \Netgen\BlockManager\API\Values\QueryUpdateStruct
     */
    public function newQueryUpdateStruct()
    {
        return new QueryUpdateStruct();
    }
}
