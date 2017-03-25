<?php

namespace Netgen\BlockManager\Core\Service;

use Exception;
use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct as APICollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct as APICollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct as APIItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct as APIQueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct as APIQueryUpdateStruct;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryUpdateStruct;

class CollectionService extends Service implements APICollectionService
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected $mapper;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder
     */
    protected $structBuilder;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     * @param \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper $mapper
     * @param \Netgen\BlockManager\Core\Service\StructBuilder\CollectionStructBuilder $structBuilder
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     */
    public function __construct(
        Handler $persistenceHandler,
        CollectionValidator $validator,
        CollectionMapper $mapper,
        CollectionStructBuilder $structBuilder,
        ParameterMapper $parameterMapper
    ) {
        parent::__construct($persistenceHandler);

        $this->validator = $validator;
        $this->mapper = $mapper;
        $this->structBuilder = $structBuilder;
        $this->parameterMapper = $parameterMapper;

        $this->handler = $persistenceHandler->getCollectionHandler();
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
        $this->validator->validateId($collectionId, 'collectionId');

        return $this->mapper->mapCollection(
            $this->handler->loadCollection(
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
        $this->validator->validateId($collectionId, 'collectionId');

        return $this->mapper->mapCollection(
            $this->handler->loadCollection(
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
        $this->validator->validateOffsetAndLimit($offset, $limit);

        $persistenceCollections = $this->handler->loadSharedCollections(
            Value::STATUS_PUBLISHED,
            $offset,
            $limit
        );

        $collections = array();
        foreach ($persistenceCollections as $persistenceCollection) {
            $collections[] = $this->mapper->mapCollection($persistenceCollection);
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
        $this->validator->validateId($itemId, 'itemId');

        return $this->mapper->mapItem(
            $this->handler->loadItem(
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
        $this->validator->validateId($itemId, 'itemId');

        return $this->mapper->mapItem(
            $this->handler->loadItem(
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
        $this->validator->validateId($queryId, 'queryId');

        return $this->mapper->mapQuery(
            $this->handler->loadQuery(
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
        $this->validator->validateId($queryId, 'queryId');

        return $this->mapper->mapQuery(
            $this->handler->loadQuery(
                $queryId,
                Value::STATUS_DRAFT
            )
        );
    }

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct $collectionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollection(APICollectionCreateStruct $collectionCreateStruct)
    {
        $this->validator->validateCollectionCreateStruct($collectionCreateStruct);

        if ($collectionCreateStruct->name !== null) {
            if ($this->handler->collectionNameExists($collectionCreateStruct->name)) {
                throw new BadStateException('name', 'Collection with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdCollection = $this->handler->createCollection(
                new CollectionCreateStruct(
                    array(
                        'status' => Value::STATUS_DRAFT,
                        'type' => $collectionCreateStruct->type,
                        'shared' => $collectionCreateStruct->shared,
                        'name' => $collectionCreateStruct->name,
                    )
                )
            );

            foreach ($collectionCreateStruct->itemCreateStructs as $position => $itemCreateStruct) {
                $this->handler->addItem(
                    $createdCollection,
                    new ItemCreateStruct(
                        array(
                            'position' => $position,
                            'valueId' => $itemCreateStruct->valueId,
                            'valueType' => $itemCreateStruct->valueType,
                            'type' => $itemCreateStruct->type,
                        )
                    )
                );
            }

            foreach ($collectionCreateStruct->queryCreateStructs as $position => $queryCreateStruct) {
                $this->handler->addQuery(
                    $createdCollection,
                    new QueryCreateStruct(
                        array(
                            'position' => $position,
                            'identifier' => $queryCreateStruct->identifier,
                            'type' => $queryCreateStruct->queryType->getType(),
                            'parameters' => $this->parameterMapper->serializeValues(
                                $queryCreateStruct->queryType,
                                $queryCreateStruct->getParameterValues()
                            ),
                        )
                    )
                );
            }
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapCollection($createdCollection);
    }

    /**
     * Updates a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function updateCollection(Collection $collection, APICollectionUpdateStruct $collectionUpdateStruct)
    {
        if ($collection->isPublished()) {
            throw new BadStateException('collection', 'Only draft collections can be updated.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->validator->validateCollectionUpdateStruct($collectionUpdateStruct);

        if ($collectionUpdateStruct->name !== null) {
            if ($this->handler->collectionNameExists($collectionUpdateStruct->name, $persistenceCollection->id)) {
                throw new BadStateException('name', 'Collection with provided name already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedCollection = $this->handler->updateCollection(
                $persistenceCollection,
                new CollectionUpdateStruct(
                    array(
                        'name' => $collectionUpdateStruct->name,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapCollection($updatedCollection);
    }

    /**
     * Changes the type of specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $newType
     * @param \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If collection type cannot be changed
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function changeCollectionType(Collection $collection, $newType, APIQueryCreateStruct $queryCreateStruct = null)
    {
        if ($collection->isPublished()) {
            throw new BadStateException('collection', 'Type can be changed only for draft collections.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        if (!in_array($newType, array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC), true)) {
            throw new BadStateException('newType', 'New collection type must be manual or dynamic.');
        }

        if ($newType === Collection::TYPE_DYNAMIC && $queryCreateStruct === null) {
            throw new BadStateException('queryCreateStruct', 'Query create struct must be defined when converting to dynamic collection.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            foreach ($this->handler->loadCollectionQueries($persistenceCollection) as $query) {
                $this->handler->deleteQuery($query);
            }

            if ($newType === Collection::TYPE_MANUAL) {
                foreach ($this->handler->loadCollectionItems($persistenceCollection) as $index => $item) {
                    $this->handler->moveItem($item, $index);
                }
            } elseif ($newType === Collection::TYPE_DYNAMIC) {
                $this->handler->addQuery(
                    $persistenceCollection,
                    new QueryCreateStruct(
                        array(
                            'identifier' => $queryCreateStruct->identifier,
                            'type' => $queryCreateStruct->queryType->getType(),
                            'parameters' => $this->parameterMapper->serializeValues(
                                $queryCreateStruct->queryType,
                                $queryCreateStruct->getParameterValues()
                            ),
                        )
                    )
                );
            }

            $newCollection = $this->handler->updateCollection(
                $persistenceCollection,
                new CollectionUpdateStruct(
                    array(
                        'type' => $newType,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapCollection($newCollection);
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
            if ($this->handler->collectionNameExists($newName, $collection->getId())) {
                throw new BadStateException('newName', 'Collection with provided name already exists.');
            }
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), $collection->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $copiedCollection = $this->handler->copyCollection(
                $persistenceCollection,
                $newName
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapCollection($copiedCollection);
    }

    /**
     * Creates a collection draft.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param bool $discardExisting
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not published
     *                                                          If draft already exists for collection and $discardExisting is set to false
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createDraft(Collection $collection, $discardExisting = false)
    {
        if (!$collection->isPublished()) {
            throw new BadStateException('collection', 'Draft can be created only from published collections.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_PUBLISHED);

        if ($this->handler->collectionExists($persistenceCollection->id, Value::STATUS_DRAFT)) {
            if (!$discardExisting) {
                throw new BadStateException('collection', 'The provided collection already has a draft.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteCollection($persistenceCollection->id, Value::STATUS_DRAFT);
            $collectionDraft = $this->handler->createCollectionStatus($persistenceCollection, Value::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapCollection($collectionDraft);
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
        if ($collection->isPublished()) {
            throw new BadStateException('collection', 'Only draft collections can be discarded.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteCollection(
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
        if ($collection->isPublished()) {
            throw new BadStateException('collection', 'Only draft collections can be published.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteCollection($persistenceCollection->id, Value::STATUS_ARCHIVED);

            if ($this->handler->collectionExists($persistenceCollection->id, Value::STATUS_PUBLISHED)) {
                $this->handler->createCollectionStatus(
                    $this->handler->loadCollection(
                        $persistenceCollection->id,
                        Value::STATUS_PUBLISHED
                    ),
                    Value::STATUS_ARCHIVED
                );

                $this->handler->deleteCollection($persistenceCollection->id, Value::STATUS_PUBLISHED);
            }

            $publishedCollection = $this->handler->createCollectionStatus($persistenceCollection, Value::STATUS_PUBLISHED);
            $this->handler->deleteCollection($persistenceCollection->id, Value::STATUS_DRAFT);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapCollection($publishedCollection);
    }

    /**
     * Deletes a specified collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     */
    public function deleteCollection(Collection $collection)
    {
        $persistenceCollection = $this->handler->loadCollection($collection->getId(), $collection->getStatus());

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteCollection(
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
     * @param \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function addItem(Collection $collection, APIItemCreateStruct $itemCreateStruct, $position = null)
    {
        if ($collection->isPublished()) {
            throw new BadStateException('collection', 'Items can only be added to draft collections.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition(
            $position,
            'position',
            $persistenceCollection->type !== Collection::TYPE_MANUAL
        );

        $this->validator->validateItemCreateStruct($itemCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdItem = $this->handler->addItem(
                $persistenceCollection,
                new ItemCreateStruct(
                    array(
                        'position' => $position,
                        'valueId' => $itemCreateStruct->valueId,
                        'valueType' => $itemCreateStruct->valueType,
                        'type' => $itemCreateStruct->type,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapItem($createdItem);
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
        if ($item->isPublished()) {
            throw new BadStateException('item', 'Only draft items can be moved.');
        }

        $persistenceItem = $this->handler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition($position, 'position', true);

        $this->persistenceHandler->beginTransaction();

        try {
            $movedItem = $this->handler->moveItem(
                $persistenceItem,
                $position
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapItem($movedItem);
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
        if ($item->isPublished()) {
            throw new BadStateException('item', 'Only draft items can be deleted.');
        }

        $persistenceItem = $this->handler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteItem($persistenceItem);
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
     * @param \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not a draft
     *                                                          If collection is not dynamic
     *                                                          If query with specified identifier already exists within the collection
     *                                                          If position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function addQuery(Collection $collection, APIQueryCreateStruct $queryCreateStruct, $position = null)
    {
        if ($collection->isPublished()) {
            throw new BadStateException('collection', 'Queries can be added only to draft collections.');
        }

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        if ($persistenceCollection->type !== Collection::TYPE_DYNAMIC) {
            throw new BadStateException('collection', 'Queries can be added only to dynamic collections.');
        }

        $this->validator->validatePosition($position, 'position');
        $this->validator->validateQueryCreateStruct($queryCreateStruct);

        if ($this->handler->queryExists($persistenceCollection, $queryCreateStruct->identifier)) {
            throw new BadStateException('identifier', 'Query with specified identifier already exists.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdQuery = $this->handler->addQuery(
                $persistenceCollection,
                new QueryCreateStruct(
                    array(
                        'position' => $position,
                        'identifier' => $queryCreateStruct->identifier,
                        'type' => $queryCreateStruct->queryType->getType(),
                        'parameters' => $this->parameterMapper->serializeValues(
                            $queryCreateStruct->queryType,
                            $queryCreateStruct->getParameterValues()
                        ),
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapQuery($createdQuery);
    }

    /**
     * Updates a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is not a draft
     *                                                          If query with specified identifier already exists within the collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function updateQuery(Query $query, APIQueryUpdateStruct $queryUpdateStruct)
    {
        if ($query->isPublished()) {
            throw new BadStateException('query', 'Only draft queries can be updated.');
        }

        $persistenceQuery = $this->handler->loadQuery($query->getId(), Value::STATUS_DRAFT);
        $persistenceCollection = $this->handler->loadCollection($persistenceQuery->collectionId, Value::STATUS_DRAFT);

        $this->validator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        if ($queryUpdateStruct->identifier !== null && $queryUpdateStruct->identifier !== $persistenceQuery->identifier) {
            if ($this->handler->queryExists($persistenceCollection, $queryUpdateStruct->identifier)) {
                throw new BadStateException('identifier', 'Query with specified identifier already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedQuery = $this->handler->updateQuery(
                $persistenceQuery,
                new QueryUpdateStruct(
                    array(
                        'identifier' => $queryUpdateStruct->identifier,
                        'parameters' => $this->parameterMapper->serializeValues(
                            $query->getQueryType(),
                            $queryUpdateStruct->getParameterValues()
                        ) + $persistenceQuery->parameters,
                    )
                )
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapQuery($updatedQuery);
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
        if ($query->isPublished()) {
            throw new BadStateException('query', 'Only draft queries can be moved.');
        }

        $persistenceQuery = $this->handler->loadQuery($query->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition($position, 'position', true);

        $this->persistenceHandler->beginTransaction();

        try {
            $movedQuery = $this->handler->moveQuery(
                $persistenceQuery,
                $position
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapQuery($movedQuery);
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
        if ($query->isPublished()) {
            throw new BadStateException('query', 'Only draft queries can be deleted.');
        }

        $persistenceQuery = $this->handler->loadQuery($query->getId(), Value::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->handler->deleteQuery($persistenceQuery);
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
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct
     */
    public function newCollectionCreateStruct($type, $name = null)
    {
        return $this->structBuilder->newCollectionCreateStruct($type, $name);
    }

    /**
     * Creates a new collection update struct.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct
     */
    public function newCollectionUpdateStruct()
    {
        return $this->structBuilder->newCollectionUpdateStruct();
    }

    /**
     * Creates a new item create struct.
     *
     * @param int $type
     * @param int|string $valueId
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct
     */
    public function newItemCreateStruct($type, $valueId, $valueType)
    {
        return $this->structBuilder->newItemCreateStruct($type, $valueId, $valueType);
    }

    /**
     * Creates a new query create struct.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType, $identifier)
    {
        return $this->structBuilder->newQueryCreateStruct($queryType, $identifier);
    }

    /**
     * Creates a new query update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct
     */
    public function newQueryUpdateStruct(Query $query = null)
    {
        return $this->structBuilder->newQueryUpdateStruct($query);
    }
}
