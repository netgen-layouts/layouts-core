<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;

class CollectionMapper extends Mapper
{
    /**
     * Builds the API collection value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function mapCollection(PersistenceCollection $collection)
    {
        $persistenceItems = $this->persistenceHandler->getCollectionHandler()->loadCollectionItems(
            $collection->id,
            $collection->status
        );

        $manualItems = array();
        $overrideItems = array();
        foreach ($persistenceItems as $persistenceItem) {
            if ($persistenceItem->type === APIItem::TYPE_MANUAL) {
                $manualItems[] = $this->mapItem($persistenceItem);
            } else {
                $overrideItems[] = $this->mapItem($persistenceItem);
            }
        }

        $persistenceQueries = $this->persistenceHandler->getCollectionHandler()->loadCollectionQueries(
            $collection->id,
            $collection->status
        );

        $queries = array();
        foreach ($persistenceQueries as $persistenceQuery) {
            $queries[] = $this->mapQuery($persistenceQuery);
        }

        return new Collection(
            array(
                'id' => $collection->id,
                'status' => $collection->status,
                'type' => $collection->type,
                'name' => $collection->name,
                'manualItems' => $manualItems,
                'overrideItems' => $overrideItems,
                'queries' => $queries,
            )
        );
    }

    /**
     * Builds the API item value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function mapItem(PersistenceItem $item)
    {
        return new Item(
            array(
                'id' => $item->id,
                'status' => $item->status,
                'collectionId' => $item->collectionId,
                'position' => $item->position,
                'type' => $item->type,
                'valueId' => $item->valueId,
                'valueType' => $item->valueType,
            )
        );
    }

    /**
     * Builds the API query value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function mapQuery(PersistenceQuery $query)
    {
        return new Query(
            array(
                'id' => $query->id,
                'status' => $query->status,
                'collectionId' => $query->collectionId,
                'position' => $query->position,
                'identifier' => $query->identifier,
                'type' => $query->type,
                'parameters' => $query->parameters,
            )
        );
    }
}
