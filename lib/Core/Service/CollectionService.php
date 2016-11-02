<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
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
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $collectionValidator
     * @param \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper $collectionMapper
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(
        CollectionValidator $collectionValidator,
        CollectionMapper $collectionMapper,
        ParameterMapper $parameterMapper,
        Handler $persistenceHandler,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->collectionValidator = $collectionValidator;
        $this->collectionMapper = $collectionMapper;
        $this->parameterMapper = $parameterMapper;
        $this->persistenceHandler = $persistenceHandler;
        $this->queryTypeRegistry = $queryTypeRegistry;

        $this->collectionHandler = $persistenceHandler->getCollectionHandler();
    }

    /**
     * Loads a collection with specified ID.
     *
     * @param int|string $collectionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollection($collectionId)
    {
        $this->collectionValidator->validateId($collectionId, 'collectionId');

        return $this->collectionMapper->mapCollection(
            $this->collectionHandler->loadCollection(
                $collectionId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads a collection draft with specified ID.
     *
     * @param int|string $collectionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function loadCollectionDraft($collectionId)
    {
        $this->collectionValidator->validateId($collectionId, 'collectionId');

        return $this->collectionMapper->mapCollection(
            $this->collectionHandler->loadCollection(
                $collectionId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads all shared collections.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    public function loadSharedCollections($offset = 0, $limit = null)
    {
        $this->collectionValidator->validateOffsetAndLimit($offset, $limit);

        $persistenceCollections = $this->collectionHandler->loadSharedCollections(
            Value::STATUS_PUBLISHED,
            $offset,
            $limit
        );

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
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItem($itemId)
    {
        $this->collectionValidator->validateId($itemId, 'itemId');

        return $this->collectionMapper->mapItem(
            $this->collectionHandler->loadItem(
                $itemId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads an item draft with specified ID.
     *
     * @param int|string $itemId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function loadItemDraft($itemId)
    {
        $this->collectionValidator->validateId($itemId, 'itemId');

        return $this->collectionMapper->mapItem(
            $this->collectionHandler->loadItem(
                $itemId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQuery($queryId)
    {
        $this->collectionValidator->validateId($queryId, 'queryId');

        return $this->collectionMapper->mapQuery(
            $this->collectionHandler->loadQuery(
                $queryId,
                Value::STATUS_PUBLISHED
            )
        );
    }

    /**
     * Loads a query with specified ID.
     *
     * @param int|string $queryId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function loadQueryDraft($queryId)
    {
        $this->collectionValidator->validateId($queryId, 'queryId');

        return $this->collectionMapper->mapQuery(
            $this->collectionHandler->loadQuery(
                $queryId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\CollectionCreateStruct $collectionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct)
    {
        $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);

        if ($collectionCreateStruct->name !== null) {
            if ($this->collectionHandler->collectionNameExists($collectionCreateStruct->name)) {
                throw new BadStateException('name', 'Collection with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdCollection = $this->collectionHandler->createCollection(
                $collectionCreateStruct,
                Value::STATUS_DRAFT
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, CollectionUpdateStruct $collectionUpdateStruct)
    {
        if ($collection->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('collection', 'Only draft collections can be updated.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->collectionValidator->validateCollectionUpdateStruct($collectionUpdateStruct);

        if ($collectionUpdateStruct->name !== null) {
            if ($this->collectionHandler->collectionNameExists($collectionUpdateStruct->name, $persistenceCollection->id)) {
                throw new BadStateException('name', 'Collection with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedCollection = $this->collectionHandler->updateCollection(
                $persistenceCollection,
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
     * Changes the type of specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $newType
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If collection type cannot be changed
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function changeCollectionType(Collection $collection, $newType, QueryCreateStruct $queryCreateStruct = null)
    {
        if ($collection->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('collection', 'Type can be changed only for draft collections.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        if (!in_array($newType, array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC))) {
            throw new BadStateException('newType', 'New collection type must be manual or dynamic.');
        }

        if ($newType === Collection::TYPE_DYNAMIC && $queryCreateStruct === null) {
            throw new BadStateException('queryCreateStruct', 'Query create struct must be defined when converting to dynamic collection.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $newCollection = $this->collectionHandler->changeCollectionType(
                $persistenceCollection,
                $newType,
                $queryCreateStruct
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($newCollection);
    }

    /**
     * Copies a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param string $newName
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection, $newName = null)
    {
        if ($newName !== null) {
            if ($this->collectionHandler->collectionNameExists($newName, $collection->getId())) {
                throw new BadStateException('name', 'Collection with provided name already exists.');
            }
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedCollection = $this->collectionHandler->copyCollection(
                $persistenceCollection,
                $newName
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($copiedCollection);
    }

    /**
     * Creates a collection draft.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not published
     *                                                          If draft already exists for collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createDraft(Collection $collection)
    {
        if ($collection->getStatus() !== Value::STATUS_PUBLISHED) {
            throw new BadStateException('collection', 'Draft can be created only from published collections.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_PUBLISHED);

        if ($this->collectionHandler->collectionExists($persistenceCollection->id, Value::STATUS_DRAFT)) {
            throw new BadStateException('collection', 'The provided collection already has a draft.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Value::STATUS_DRAFT);
            $collectionDraft = $this->collectionHandler->createCollectionStatus($persistenceCollection, Value::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->collectionMapper->mapCollection($collectionDraft);
    }

    /**
     * Discards a collection draft.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     */
    public function discardDraft(Collection $collection)
    {
        if ($collection->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('collection', 'Only draft collections can be discarded.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection(
                $persistenceCollection->id,
                Value::STATUS_DRAFT
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
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
        if ($collection->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('collection', 'Only draft collections can be published.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Value::STATUS_ARCHIVED);

            if ($this->collectionHandler->collectionExists($persistenceCollection->id, Value::STATUS_PUBLISHED)) {
                $this->collectionHandler->createCollectionStatus(
                    $this->collectionHandler->loadCollection(
                        $persistenceCollection->id,
                        Value::STATUS_PUBLISHED
                    ),
                    Value::STATUS_ARCHIVED
                );

                $this->collectionHandler->deleteCollection($persistenceCollection->id, Value::STATUS_PUBLISHED);
            }

            $publishedCollection = $this->collectionHandler->createCollectionStatus($persistenceCollection, Value::STATUS_PUBLISHED);
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Value::STATUS_DRAFT);
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
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     */
    public function deleteCollection(Collection $collection)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), $collection->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection(
                $persistenceCollection->id
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct, $position = null)
    {
        if ($collection->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('collection', 'Items can only be added to draft collections.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->collectionValidator->validatePosition(
            $position,
            'position',
            $persistenceCollection->type !== Collection::TYPE_MANUAL
        );

        $this->collectionValidator->validateItemCreateStruct($itemCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdItem = $this->collectionHandler->addItem(
                $persistenceCollection,
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     *                                                          If position is out of range (for manual collections)
     */
    public function moveItem(Item $item, $position)
    {
        if ($item->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('item', 'Only draft items can be moved.');
        }

        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->collectionValidator->validatePosition($position, 'position', true);

        $this->persistenceHandler->beginTransaction();

        try {
            $movedItem = $this->collectionHandler->moveItem(
                $persistenceItem,
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If item is not a draft
     */
    public function deleteItem(Item $item)
    {
        if ($item->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('item', 'Only draft items can be deleted.');
        }

        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteItem($persistenceItem);
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If query with specified identifier already exists within the collection
     *                                                          If position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function addQuery(Collection $collection, QueryCreateStruct $queryCreateStruct, $position = null)
    {
        if ($collection->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('query', 'Queries can be added only to draft collections.');
        }

        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->collectionValidator->validatePosition($position, 'position');
        $this->collectionValidator->validateQueryCreateStruct($queryCreateStruct);

        if ($this->collectionHandler->queryIdentifierExists($persistenceCollection, $queryCreateStruct->identifier)) {
            throw new BadStateException('identifier', 'Query with specified identifier already exists.');
        }

        $queryType = $this->queryTypeRegistry->getQueryType(
            $queryCreateStruct->type
        );

        $clonedQueryCreateStruct = clone $queryCreateStruct;
        $clonedQueryCreateStruct->setParameters(
            $this->parameterMapper->serializeValues(
                $queryType->getParameters(),
                $queryCreateStruct->getParameters()
            )
        );

        $this->persistenceHandler->beginTransaction();

        try {
            $createdQuery = $this->collectionHandler->addQuery(
                $persistenceCollection,
                $clonedQueryCreateStruct,
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is not a draft
     *                                                          If query with specified identifier already exists within the collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct)
    {
        if ($query->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('query', 'Only draft queries can be updated.');
        }

        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), Value::STATUS_DRAFT);
        $persistenceCollection = $this->collectionHandler->loadCollection($persistenceQuery->collectionId, Value::STATUS_DRAFT);

        $this->collectionValidator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        if ($queryUpdateStruct->identifier !== null && $queryUpdateStruct->identifier !== $persistenceQuery->identifier) {
            if ($this->collectionHandler->queryIdentifierExists($persistenceCollection, $queryUpdateStruct->identifier)) {
                throw new BadStateException('identifier', 'Query with specified identifier already exists.');
            }
        }

        $clonedQueryUpdateStruct = clone $queryUpdateStruct;
        $clonedQueryUpdateStruct->setParameters(
            $this->parameterMapper->serializeValues(
                $query->getQueryType()->getParameters(),
                $queryUpdateStruct->getParameters()
            )
        );

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedQuery = $this->collectionHandler->updateQuery(
                $persistenceQuery,
                $clonedQueryUpdateStruct
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is not a draft
     *                                                          If position is out of range
     */
    public function moveQuery(Query $query, $position)
    {
        if ($query->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('query', 'Only draft queries can be moved.');
        }

        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), Value::STATUS_DRAFT);

        $this->collectionValidator->validatePosition($position, 'position', true);

        $this->persistenceHandler->beginTransaction();

        try {
            $movedQuery = $this->collectionHandler->moveQuery(
                $persistenceQuery,
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
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is not a draft
     */
    public function deleteQuery(Query $query)
    {
        if ($query->getStatus() !== Value::STATUS_DRAFT) {
            throw new BadStateException('query', 'Only draft queries can be deleted.');
        }

        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteQuery($persistenceQuery);
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
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\QueryCreateStruct
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType, $identifier)
    {
        $queryCreateStruct = new QueryCreateStruct(
            array(
                'identifier' => $identifier,
                'type' => $queryType->getType(),
            )
        );

        $queryCreateStruct->fillValues(
            $queryType->getParameters(),
            $queryType->getConfig()->getDefaultParameters()
        );

        return $queryCreateStruct;
    }

    /**
     * Creates a new query update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\API\Values\QueryUpdateStruct
     */
    public function newQueryUpdateStruct(Query $query = null)
    {
        $queryUpdateStruct = new QueryUpdateStruct();

        if (!$query instanceof Query) {
            return $queryUpdateStruct;
        }

        $queryUpdateStruct->identifier = $query->getIdentifier();

        $queryType = $query->getQueryType();
        $queryUpdateStruct->fillValues(
            $queryType->getParameters(),
            $query->getParameters(),
            false
        );

        return $queryUpdateStruct;
    }
}
