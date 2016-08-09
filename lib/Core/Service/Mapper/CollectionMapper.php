<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\CollectionDraft;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\ItemDraft;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Collection\QueryDraft;

class CollectionMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(Handler $persistenceHandler, QueryTypeRegistryInterface $queryTypeRegistry)
    {
        parent::__construct($persistenceHandler);

        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Builds the API collection value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection|\Netgen\BlockManager\API\Values\Collection\CollectionDraft
     */
    public function mapCollection(PersistenceCollection $collection)
    {
        $persistenceItems = $this->persistenceHandler->getCollectionHandler()->loadCollectionItems(
            $collection
        );

        $items = array();
        foreach ($persistenceItems as $persistenceItem) {
            $items[] = $this->mapItem($persistenceItem);
        }

        $persistenceQueries = $this->persistenceHandler->getCollectionHandler()->loadCollectionQueries(
            $collection
        );

        $queries = array();
        foreach ($persistenceQueries as $persistenceQuery) {
            $queries[] = $this->mapQuery($persistenceQuery);
        }

        $collectionData = array(
            'id' => $collection->id,
            'status' => $collection->status,
            'type' => $collection->type,
            'name' => $collection->name,
            'items' => $items,
            'queries' => $queries,
        );

        return $collection->status === PersistenceCollection::STATUS_PUBLISHED ?
            new Collection($collectionData) :
            new CollectionDraft($collectionData);
    }

    /**
     * Builds the API item value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item|\Netgen\BlockManager\API\Values\Collection\ItemDraft
     */
    public function mapItem(PersistenceItem $item)
    {
        $itemData = array(
            'id' => $item->id,
            'status' => $item->status,
            'collectionId' => $item->collectionId,
            'position' => $item->position,
            'type' => $item->type,
            'valueId' => $item->valueId,
            'valueType' => $item->valueType,
        );

        return $item->status === PersistenceCollection::STATUS_PUBLISHED ?
            new Item($itemData) :
            new ItemDraft($itemData);
    }

    /**
     * Builds the API query value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query|\Netgen\BlockManager\API\Values\Collection\QueryDraft
     */
    public function mapQuery(PersistenceQuery $query)
    {
        $queryData = array(
            'id' => $query->id,
            'status' => $query->status,
            'collectionId' => $query->collectionId,
            'position' => $query->position,
            'identifier' => $query->identifier,
            'queryType' => $this->queryTypeRegistry->getQueryType(
                $query->type
            ),
            'parameters' => $query->parameters,
        );

        return $query->status === PersistenceCollection::STATUS_PUBLISHED ?
            new Query($queryData) :
            new QueryDraft($queryData);
    }
}
