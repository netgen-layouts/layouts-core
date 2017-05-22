<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Query;
use Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\QueryUpdateStruct;

class CollectionHandler implements CollectionHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler
     */
    protected $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    protected $positionHelper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\CollectionQueryHandler $queryHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\CollectionMapper $collectionMapper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper $positionHelper
     */
    public function __construct(
        CollectionQueryHandler $queryHandler,
        CollectionMapper $collectionMapper,
        PositionHelper $positionHelper
    ) {
        $this->queryHandler = $queryHandler;
        $this->collectionMapper = $collectionMapper;
        $this->positionHelper = $positionHelper;
    }

    /**
     * Loads a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If collection with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function loadCollection($collectionId, $status)
    {
        $data = $this->queryHandler->loadCollectionData($collectionId, $status);

        if (empty($data)) {
            throw new NotFoundException('collection', $collectionId);
        }

        $data = $this->collectionMapper->mapCollections($data);

        return reset($data);
    }

    /**
     * Loads an item with specified ID.
     *
     * @param int|string $itemId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If item with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function loadItem($itemId, $status)
    {
        $data = $this->queryHandler->loadItemData($itemId, $status);

        if (empty($data)) {
            throw new NotFoundException('item', $itemId);
        }

        $data = $this->collectionMapper->mapItems($data);

        return reset($data);
    }

    /**
     * Loads all items that belong to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item[]
     */
    public function loadCollectionItems(Collection $collection)
    {
        return $this->collectionMapper->mapItems(
            $this->queryHandler->loadCollectionItemsData($collection)
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
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function loadQuery($queryId, $status)
    {
        $data = $this->queryHandler->loadQueryData($queryId, $status);

        if (empty($data)) {
            throw new NotFoundException('query', $queryId);
        }

        return $this->collectionMapper->mapQuery($data);
    }

    /**
     * Loads the query that belongs to collection with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If query for specified collection does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function loadCollectionQuery(Collection $collection)
    {
        $data = $this->queryHandler->loadCollectionQueryData($collection);

        if (empty($data)) {
            throw new NotFoundException('query for collection', $collection->id);
        }

        return $this->collectionMapper->mapQuery($data);
    }

    /**
     * Returns if collection with specified ID exists.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return bool
     */
    public function collectionExists($collectionId, $status)
    {
        return $this->queryHandler->collectionExists($collectionId, $status);
    }

    /**
     * Creates a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\CollectionCreateStruct $collectionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollection(CollectionCreateStruct $collectionCreateStruct)
    {
        $newCollection = new Collection(
            array(
                'status' => $collectionCreateStruct->status,
            )
        );

        return $this->queryHandler->createCollection($newCollection);
    }

    /**
     * Copies a collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function copyCollection(Collection $collection)
    {
        $newCollection = clone $collection;
        $newCollection->id = null;

        $newCollection = $this->queryHandler->createCollection($newCollection);

        $collectionItems = $this->loadCollectionItems($collection);

        foreach ($collectionItems as $collectionItem) {
            $newItem = clone $collectionItem;

            $newItem->id = null;
            $newItem->collectionId = $newCollection->id;

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

            $newQuery->id = null;
            $newQuery->collectionId = $newCollection->id;

            $this->queryHandler->addQuery($newQuery);
        }

        return $newCollection;
    }

    /**
     * Creates a new collection status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public function createCollectionStatus(Collection $collection, $newStatus)
    {
        $newCollection = clone $collection;
        $newCollection->status = $newStatus;

        $this->queryHandler->createCollection($newCollection);

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

            $this->queryHandler->addQuery($newQuery);
        }

        return $newCollection;
    }

    /**
     * Deletes a collection with specified ID.
     *
     * @param int|string $collectionId
     * @param int $status
     */
    public function deleteCollection($collectionId, $status = null)
    {
        $this->queryHandler->deleteCollectionItems($collectionId, $status);
        $this->queryHandler->deleteCollectionQuery($collectionId, $status);
        $this->queryHandler->deleteCollection($collectionId, $status);
    }

    /**
     * Adds an item to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\Collection\ItemCreateStruct $itemCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function addItem(Collection $collection, ItemCreateStruct $itemCreateStruct)
    {
        $isDynamic = true;
        try {
            $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            $isDynamic = false;
        }

        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperItemConditions(
                $collection->id,
                $collection->status
            ),
            $itemCreateStruct->position,
            $isDynamic
        );

        $newItem = new Item(
            array(
                'collectionId' => $collection->id,
                'position' => $position,
                'type' => $itemCreateStruct->type,
                'valueId' => $itemCreateStruct->valueId,
                'valueType' => $itemCreateStruct->valueType,
                'status' => $collection->status,
            )
        );

        return $this->queryHandler->addItem($newItem);
    }

    /**
     * Moves an item to specified position in the collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     * @param int $position
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided position is out of range (for manual collections)
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Item
     */
    public function moveItem(Item $item, $position)
    {
        $collection = $this->loadCollection($item->collectionId, $item->status);

        $isDynamic = true;
        try {
            $this->loadCollectionQuery($collection);
        } catch (NotFoundException $e) {
            $isDynamic = false;
        }

        $movedItem = clone $item;

        $movedItem->position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperItemConditions(
                $collection->id,
                $item->status
            ),
            $item->position,
            $position,
            $isDynamic
        );

        $this->queryHandler->updateItem($movedItem);

        return $movedItem;
    }

    /**
     * Removes an item.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     */
    public function deleteItem(Item $item)
    {
        $this->queryHandler->deleteItem($item->id, $item->status);

        $this->positionHelper->removePosition(
            $this->getPositionHelperItemConditions(
                $item->collectionId,
                $item->status
            ),
            $item->position
        );
    }

    /**
     * Adds a query to collection.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     * @param \Netgen\BlockManager\Persistence\Values\Collection\QueryCreateStruct $queryCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If collection already has a query
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function addQuery(Collection $collection, QueryCreateStruct $queryCreateStruct)
    {
        try {
            $this->loadCollectionQuery($collection);

            throw new BadStateException('collection', 'Provided collection already has a query.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $newQuery = new Query(
            array(
                'collectionId' => $collection->id,
                'type' => $queryCreateStruct->type,
                'parameters' => $queryCreateStruct->parameters,
                'status' => $collection->status,
            )
        );

        return $this->queryHandler->addQuery($newQuery);
    }

    /**
     * Updates a query with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Persistence\Values\Collection\QueryUpdateStruct $queryUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Collection\Query
     */
    public function updateQuery(Query $query, QueryUpdateStruct $queryUpdateStruct)
    {
        $updatedQuery = clone $query;

        if (is_array($queryUpdateStruct->parameters)) {
            $updatedQuery->parameters = $queryUpdateStruct->parameters;
        }

        $this->queryHandler->updateQuery($updatedQuery);

        return $updatedQuery;
    }

    /**
     * Removes a query.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     */
    public function deleteQuery(Query $query)
    {
        $this->queryHandler->deleteQuery($query->id, $query->status);
    }

    /**
     * Builds the condition array that will be used with position helper and items in collections.
     *
     * @param int|string $collectionId
     * @param int $status
     *
     * @return array
     */
    protected function getPositionHelperItemConditions($collectionId, $status)
    {
        return array(
            'table' => 'ngbm_collection_item',
            'column' => 'position',
            'conditions' => array(
                'collection_id' => $collectionId,
                'status' => $status,
            ),
        );
    }
}
