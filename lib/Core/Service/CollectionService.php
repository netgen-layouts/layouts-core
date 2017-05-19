<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct as APICollectionCreateStruct;
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
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function createCollection(APICollectionCreateStruct $collectionCreateStruct)
    {
        $this->validator->validateCollectionCreateStruct($collectionCreateStruct);

        $createdCollection = $this->transaction(
            function () use ($collectionCreateStruct) {
                $createdCollection = $this->handler->createCollection(
                    new CollectionCreateStruct(
                        array(
                            'status' => Value::STATUS_DRAFT,
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

                if ($collectionCreateStruct->queryCreateStruct instanceof APIQueryCreateStruct) {
                    $queryCreateStruct = $collectionCreateStruct->queryCreateStruct;

                    $this->handler->addQuery(
                        $createdCollection,
                        new QueryCreateStruct(
                            array(
                                'type' => $queryCreateStruct->queryType->getType(),
                                'parameters' => $this->parameterMapper->serializeValues(
                                    $queryCreateStruct->queryType,
                                    $queryCreateStruct->getParameterValues()
                                ),
                            )
                        )
                    );
                }

                return $createdCollection;
            }
        );

        return $this->mapper->mapCollection($createdCollection);
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

        $this->transaction(
            function () use ($collection, $persistenceCollection, $newType, $queryCreateStruct) {
                if ($collection->getType() === Collection::TYPE_DYNAMIC) {
                    $query = $this->handler->loadCollectionQuery($persistenceCollection);
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
                                'type' => $queryCreateStruct->queryType->getType(),
                                'parameters' => $this->parameterMapper->serializeValues(
                                    $queryCreateStruct->queryType,
                                    $queryCreateStruct->getParameterValues()
                                ),
                            )
                        )
                    );
                }
            }
        );

        $persistenceCollection = $this->handler->loadCollection($collection->getId(), Value::STATUS_DRAFT);

        return $this->mapper->mapCollection($persistenceCollection);
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
        $persistenceCollection = $this->handler->loadCollection($collection->getId(), $collection->getStatus());

        $copiedCollection = $this->transaction(
            function () use ($persistenceCollection) {
                return $this->handler->copyCollection($persistenceCollection);
            }
        );

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

        $collectionDraft = $this->transaction(
            function () use ($persistenceCollection) {
                $this->handler->deleteCollection($persistenceCollection->id, Value::STATUS_DRAFT);

                return $this->handler->createCollectionStatus($persistenceCollection, Value::STATUS_DRAFT);
            }
        );

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

        $this->transaction(
            function () use ($persistenceCollection) {
                $this->handler->deleteCollection(
                    $persistenceCollection->id,
                    Value::STATUS_DRAFT
                );
            }
        );
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

        $publishedCollection = $this->transaction(
            function () use ($persistenceCollection) {
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

                return $publishedCollection;
            }
        );

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

        $this->transaction(
            function () use ($persistenceCollection) {
                $this->handler->deleteCollection(
                    $persistenceCollection->id
                );
            }
        );
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
            $collection->getType() !== Collection::TYPE_MANUAL
        );

        $this->validator->validateItemCreateStruct($itemCreateStruct);

        $createdItem = $this->transaction(
            function () use ($persistenceCollection, $position, $itemCreateStruct) {
                return $this->handler->addItem(
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
            }
        );

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
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function moveItem(Item $item, $position)
    {
        if ($item->isPublished()) {
            throw new BadStateException('item', 'Only draft items can be moved.');
        }

        $persistenceItem = $this->handler->loadItem($item->getId(), Value::STATUS_DRAFT);

        $this->validator->validatePosition($position, 'position', true);

        $movedItem = $this->transaction(
            function () use ($persistenceItem, $position) {
                return $this->handler->moveItem(
                    $persistenceItem,
                    $position
                );
            }
        );

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

        $this->transaction(
            function () use ($persistenceItem) {
                $this->handler->deleteItem($persistenceItem);
            }
        );
    }

    /**
     * Updates a query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function updateQuery(Query $query, APIQueryUpdateStruct $queryUpdateStruct)
    {
        if ($query->isPublished()) {
            throw new BadStateException('query', 'Only draft queries can be updated.');
        }

        $persistenceQuery = $this->handler->loadQuery($query->getId(), Value::STATUS_DRAFT);

        $this->validator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        $updatedQuery = $this->transaction(
            function () use ($query, $persistenceQuery, $queryUpdateStruct) {
                return $this->handler->updateQuery(
                    $persistenceQuery,
                    new QueryUpdateStruct(
                        array(
                            'type' => $queryUpdateStruct->queryType !== null ?
                                $queryUpdateStruct->queryType->getType() :
                                null,
                            'parameters' => $this->parameterMapper->serializeValues(
                                $query->getQueryType(),
                                $queryUpdateStruct->getParameterValues(),
                                $persistenceQuery->parameters
                            ),
                        )
                    )
                );
            }
        );

        return $this->mapper->mapQuery($updatedQuery);
    }

    /**
     * Creates a new collection create struct.
     *
     * @param int $type
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct
     */
    public function newCollectionCreateStruct($type)
    {
        return $this->structBuilder->newCollectionCreateStruct($type);
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
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType)
    {
        return $this->structBuilder->newQueryCreateStruct($queryType);
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
