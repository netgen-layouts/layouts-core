<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Service\CollectionService as APICollectionService;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionDraft;
use Netgen\BlockManager\API\Values\Collection\ItemDraft;
use Netgen\BlockManager\API\Values\Collection\QueryDraft;
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
                Collection::STATUS_PUBLISHED
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
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function loadCollectionDraft($collectionId)
    {
        $this->collectionValidator->validateId($collectionId, 'collectionId');

        return $this->collectionMapper->mapCollection(
            $this->collectionHandler->loadCollection(
                $collectionId,
                Collection::STATUS_DRAFT
            )
        );
    }

    /**
     * Loads all named collections.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    public function loadNamedCollections()
    {
        $persistenceCollections = $this->collectionHandler->loadNamedCollections(Collection::STATUS_PUBLISHED);

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
                Collection::STATUS_PUBLISHED
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
     * @return \Netgen\BlockManager\API\Values\Collection\ItemDraft
     */
    public function loadItemDraft($itemId)
    {
        $this->collectionValidator->validateId($itemId, 'itemId');

        return $this->collectionMapper->mapItem(
            $this->collectionHandler->loadItem(
                $itemId,
                Collection::STATUS_DRAFT
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
                Collection::STATUS_PUBLISHED
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
     * @return \Netgen\BlockManager\API\Values\Collection\QueryDraft
     */
    public function loadQueryDraft($queryId)
    {
        $this->collectionValidator->validateId($queryId, 'queryId');

        return $this->collectionMapper->mapQuery(
            $this->collectionHandler->loadQuery(
                $queryId,
                Collection::STATUS_DRAFT
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
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
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
                $collectionCreateStruct,
                Collection::STATUS_DRAFT
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
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param \Netgen\BlockManager\API\Values\CollectionUpdateStruct $collectionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection is not named
     *                                                          If collection with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function updateCollection(CollectionDraft $collection, CollectionUpdateStruct $collectionUpdateStruct)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Collection::STATUS_DRAFT);

        $this->collectionValidator->validateCollectionUpdateStruct($collectionUpdateStruct);

        if ($persistenceCollection->type !== Collection::TYPE_NAMED) {
            throw new BadStateException('collection', 'Only named collections can be updated.');
        }

        if ($collectionUpdateStruct->name !== null) {
            if ($this->collectionHandler->namedCollectionExists($collectionUpdateStruct->name, $persistenceCollection->id)) {
                throw new BadStateException('name', 'Named collection with provided name already exists.');
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
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param int $newType
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection type cannot be changed
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function changeCollectionType(CollectionDraft $collection, $newType, APIQueryCreateStruct $queryCreateStruct = null)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Collection::STATUS_DRAFT);

        if (!in_array($newType, array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC))) {
            throw new BadStateException('newType', 'New collection type must be manual or dynamic.');
        }

        if ($persistenceCollection->type === Collection::TYPE_NAMED) {
            throw new BadStateException('collection', 'Only manual or dynamic collections can be converted.');
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

        return $this->collectionMapper->mapCollection(
            $this->collectionHandler->loadCollection($copiedCollectionId, $persistenceCollection->status)
        );
    }

    /**
     * Creates a collection draft.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If draft already exists for collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function createDraft(Collection $collection)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Collection::STATUS_PUBLISHED);

        if ($this->collectionHandler->collectionExists($persistenceCollection->id, Collection::STATUS_DRAFT)) {
            throw new BadStateException('collection', 'The provided collection already has a draft.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_DRAFT);
            $collectionDraft = $this->collectionHandler->createCollectionStatus($persistenceCollection, Collection::STATUS_DRAFT);
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
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     */
    public function discardDraft(CollectionDraft $collection)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Collection::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection(
                $persistenceCollection->id,
                Collection::STATUS_DRAFT
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
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function publishCollection(CollectionDraft $collection)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Collection::STATUS_DRAFT);

        $this->persistenceHandler->beginTransaction();

        try {
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_ARCHIVED);

            if ($this->collectionHandler->collectionExists($persistenceCollection->id, Collection::STATUS_PUBLISHED)) {
                $this->collectionHandler->createCollectionStatus(
                    $this->collectionHandler->loadCollection(
                        $persistenceCollection->id,
                        Collection::STATUS_PUBLISHED
                    ),
                    Collection::STATUS_ARCHIVED
                );

                $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_PUBLISHED);
            }

            $publishedCollection = $this->collectionHandler->createCollectionStatus($persistenceCollection, Collection::STATUS_PUBLISHED);
            $this->collectionHandler->deleteCollection($persistenceCollection->id, Collection::STATUS_DRAFT);
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
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param \Netgen\BlockManager\API\Values\ItemCreateStruct $itemCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemDraft
     */
    public function addItem(CollectionDraft $collection, ItemCreateStruct $itemCreateStruct, $position = null)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Collection::STATUS_DRAFT);

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
     * @param \Netgen\BlockManager\API\Values\Collection\ItemDraft $item
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If position is out of range (for manual collections)
     */
    public function moveItem(ItemDraft $item, $position)
    {
        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), Collection::STATUS_DRAFT);

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
     * @param \Netgen\BlockManager\API\Values\Collection\ItemDraft $item
     */
    public function deleteItem(ItemDraft $item)
    {
        $persistenceItem = $this->collectionHandler->loadItem($item->getId(), Collection::STATUS_DRAFT);

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
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param \Netgen\BlockManager\API\Values\QueryCreateStruct $queryCreateStruct
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection the query is in is not named
     *                                                          If query with specified identifier already exists within the collection
     *                                                          If position is out of range
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryDraft
     */
    public function addQuery(CollectionDraft $collection, APIQueryCreateStruct $queryCreateStruct, $position = null)
    {
        $persistenceCollection = $this->collectionHandler->loadCollection($collection->getId(), Collection::STATUS_DRAFT);

        if ($persistenceCollection->type !== Collection::TYPE_NAMED) {
            throw new BadStateException('queryCreateStruct', 'Query can only be added to a named collection.');
        }

        $this->collectionValidator->validatePosition($position, 'position');

        $this->collectionValidator->validateQueryCreateStruct($queryCreateStruct);

        if ($this->collectionHandler->queryIdentifierExists($persistenceCollection, $queryCreateStruct->identifier)) {
            throw new BadStateException('identifier', 'Query with specified identifier already exists.');
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $createdQuery = $this->collectionHandler->addQuery(
                $persistenceCollection,
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
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     * @param \Netgen\BlockManager\API\Values\QueryUpdateStruct $queryUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If query with specified identifier already exists within the collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryDraft
     */
    public function updateQuery(QueryDraft $query, APIQueryUpdateStruct $queryUpdateStruct)
    {
        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), Collection::STATUS_DRAFT);
        $persistenceCollection = $this->collectionHandler->loadCollection($persistenceQuery->collectionId, Collection::STATUS_DRAFT);

        $this->collectionValidator->validateQueryUpdateStruct($query, $queryUpdateStruct);

        if ($queryUpdateStruct->identifier !== null && $queryUpdateStruct->identifier !== $persistenceQuery->identifier) {
            if ($this->collectionHandler->queryIdentifierExists($persistenceCollection, $queryUpdateStruct->identifier)) {
                throw new BadStateException('identifier', 'Query with specified identifier already exists.');
            }
        }

        $this->persistenceHandler->beginTransaction();

        try {
            $updatedQuery = $this->collectionHandler->updateQuery(
                $persistenceQuery,
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
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     * @param int $position
     *
     * @throw \Netgen\BlockManager\Exception\BadStateException If collection the query is in is not named
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If position is out of range
     */
    public function moveQuery(QueryDraft $query, $position)
    {
        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), Collection::STATUS_DRAFT);

        $persistenceCollection = $this->collectionHandler->loadCollection($query->getCollectionId(), Collection::STATUS_DRAFT);

        if ($persistenceCollection->type !== Collection::TYPE_NAMED) {
            throw new BadStateException('queryCreateStruct', 'Query can only be moved inside a named collection.');
        }

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
     * @throw \Netgen\BlockManager\Exception\BadStateException If collection the query is in is not named
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryDraft $query
     */
    public function deleteQuery(QueryDraft $query)
    {
        $persistenceQuery = $this->collectionHandler->loadQuery($query->getId(), Collection::STATUS_DRAFT);

        $persistenceCollection = $this->collectionHandler->loadCollection($query->getCollectionId(), Collection::STATUS_DRAFT);

        if ($persistenceCollection->type !== Collection::TYPE_NAMED) {
            throw new BadStateException('queryCreateStruct', 'Query can only be deleted from a named collection.');
        }

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

        $queryParameters = array();

        $queryTypeParameters = $queryType->getHandler()->getParameters();
        if (is_array($queryTypeParameters)) {
            foreach ($queryTypeParameters as $parameterName => $parameter) {
                $queryParameters[$parameterName] = $parameter->getDefaultValue();

                if ($parameter instanceof CompoundParameterInterface) {
                    foreach ($parameter->getParameters() as $subParameterName => $subParameter) {
                        $queryParameters[$subParameterName] = $parameter->getDefaultValue();
                    }
                }
            }
        }

        $queryCreateStruct->setParameters($queryType->getConfig()->getDefaultQueryParameters() + $queryParameters);

        return $queryCreateStruct;
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
