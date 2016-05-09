<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Exception\BadStateException;
use Netgen\BlockManager\API\Values\Page\Block;
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
use Netgen\BlockManager\API\Exception\InvalidArgumentException;
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
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If collection with specified ID does not exist
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
     * Loads all collections belonging to the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    public function loadBlockCollections(Block $block)
    {
        $persistenceCollections = $this->collectionHandler->loadBlockCollections(
            $block->getId(),
            $block->getStatus()
        );

        $collections = array();
        foreach ($persistenceCollections as $identifier => $persistenceCollection) {
            $collections[$identifier] = $this->collectionMapper->mapCollection($persistenceCollection);
        }

        return $collections;
    }

    /**
     * Loads an item with specified ID.
     *
     * @param int|string $itemId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If item with specified ID does not exist
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
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If query with specified ID does not exist
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If named collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct)
    {
        $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);

        if (
            $collectionCreateStruct->type === Collection::TYPE_NAMED &&
            $this->collectionHandler->namedCollectionExists($collectionCreateStruct->name)
        ) {
            throw new BadStateException('name', 'Named collection with provided name already exists.');
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection is not in the draft status
     *                                                              If named collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct)
    {
        $this->collectionValidator->validateCollectionUpdateStruct($collectionUpdateStruct);

        if ($collection->getType() === Collection::TYPE_NAMED && $collectionUpdateStruct->name !== null) {
            if ($this->collectionHandler->namedCollectionExists($collectionUpdateStruct->name)) {
                throw new BadStateException('name', 'Named collection with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedCollection = $this->collectionHandler->updateCollection(
                $collection->getId(),
                $collection->getStatus(),
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
        $this->persistenceHandler->beginTransaction();

        try {
            $copiedCollection = $this->collectionHandler->copyCollection(
                $collection->getId()
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($copiedCollection);
    }

    /**
     * Creates a new collection status.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection already has the provided status
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollectionStatus(Collection $collection, $status)
    {
        if ($this->collectionHandler->collectionExists($collection->getId(), $status)) {
            throw new BadStateException('status', 'Collection already has the provided status.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdCollection = $this->collectionHandler->createCollectionStatus(
                $collection->getId(),
                $collection->getStatus(),
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
     * Publishes a collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function publishCollection(Collection $collection)
    {
        if ($collection->getStatus() !== Collection::STATUS_DRAFT) {
            throw new BadStateException('collection', 'Only collections in draft status can be published.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $collectionHandler = $this->collectionHandler;
            $collectionHandler->deleteCollection($collection->getId(), Collection::STATUS_ARCHIVED);
            $collectionHandler->updateCollectionStatus($collection->getId(), Collection::STATUS_PUBLISHED, Collection::STATUS_ARCHIVED);
            $publishedCollection = $collectionHandler->updateCollectionStatus($collection->getId(), Collection::STATUS_DRAFT, Collection::STATUS_PUBLISHED);
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
        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection(
                $collection->getId(),
                $deleteAllStatuses ? null : $collection->getStatus()
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If position is not set (for non manual collections)
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection is not a draft
     *                                                              If override item is added to manual collection
     *                                                              If item already exists in provided position (only for non manual collections)
     *                                                              If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct, $position = null)
    {
        if ($position !== null) {
            $this->collectionValidator->validatePosition($position, 'position');
        }

        $this->collectionValidator->validateItemCreateStruct($itemCreateStruct);

        if ($collection->getType() === Collection::TYPE_MANUAL) {
            if ($itemCreateStruct->type === Item::TYPE_OVERRIDE) {
                throw new BadStateException('type', 'Override item cannot be added to manual collection.');
            }
        } else {
            if ($position === null) {
                throw new InvalidArgumentException('position', 'Position must be set for non manual collections.');
            }

            if ($this->collectionHandler->itemExists($collection->getId(), $collection->getStatus(), $position)) {
                throw new BadStateException('position', 'Item already exists on that position.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdItem = $this->collectionHandler->addItem(
                $collection->getId(),
                $collection->getStatus(),
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If item is not a draft
     *                                                              If item already exists in provided position (only for non manual collections)
     *                                                              If position is out of range (for manual collections)
     */
    public function moveItem(Item $item, $position)
    {
        $this->collectionValidator->validatePosition($position, 'position');

        $collection = $this->collectionHandler->loadCollection(
            $item->getCollectionId(),
            $item->getStatus()
        );

        if ($collection->type !== Collection::TYPE_MANUAL) {
            if ($this->collectionHandler->itemExists($collection->id, $collection->status, $position)) {
                throw new BadStateException('position', 'Item already exists on that position.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $movedItem = $this->collectionHandler->moveItem(
                $item->getId(),
                $item->getStatus(),
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
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If item is not a draft
     */
    public function deleteItem(Item $item)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteItem(
                $item->getId(),
                $item->getStatus()
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If collection is not a draft
     *                                                              If query is added to manual collection
     *                                                              If query with specified identifier already exists within the collection
     *                                                              If position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function addQuery(Collection $collection, APIQueryCreateStruct $queryCreateStruct, $position = null)
    {
        if ($collection->getType() === Collection::TYPE_MANUAL) {
            throw new BadStateException('queryCreateStruct', 'Query cannot be added to manual collection.');
        }

        if ($position !== null) {
            $this->collectionValidator->validatePosition($position, 'position');
        }

        $this->collectionValidator->validateQueryCreateStruct($queryCreateStruct);

        if ($this->collectionHandler->queryExists($collection->getId(), $collection->getStatus(), $queryCreateStruct->identifier)) {
            throw new BadStateException('identifier', 'Query with specified identifier already exists.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdQuery = $this->collectionHandler->addQuery(
                $collection->getId(),
                $collection->getStatus(),
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If query is not a draft
     *                                                              If query with specified identifier already exists within the collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function updateQuery(Query $query, APIQueryUpdateStruct $queryUpdateStruct)
    {
        $this->collectionValidator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        if ($queryUpdateStruct->identifier !== null && $queryUpdateStruct->identifier !== $query->getIdentifier()) {
            if ($this->collectionHandler->queryExists($query->getCollectionId(), $query->getStatus(), $queryUpdateStruct->identifier)) {
                throw new BadStateException('identifier', 'Query with specified identifier already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedQuery = $this->collectionHandler->updateQuery(
                $query->getId(),
                $query->getStatus(),
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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If item is not a draft
     *                                                              If position is out of range
     */
    public function moveQuery(Query $query, $position)
    {
        $this->collectionValidator->validatePosition($position, 'position');

        $this->persistenceHandler->beginTransaction();

        try {
            $movedQuery = $this->collectionHandler->moveQuery(
                $query->getId(),
                $query->getStatus(),
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
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If query is not a draft
     */
    public function deleteQuery(Query $query)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteQuery(
                $query->getId(),
                $query->getStatus()
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
     * @param int $type
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
