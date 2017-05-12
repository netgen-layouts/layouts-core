<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Collection\Collection as PersistenceCollection;
use Netgen\BlockManager\Persistence\Values\Collection\Item as PersistenceItem;
use Netgen\BlockManager\Persistence\Values\Collection\Query as PersistenceQuery;

class CollectionMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(
        Handler $persistenceHandler,
        ParameterMapper $parameterMapper,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        parent::__construct($persistenceHandler);

        $this->parameterMapper = $parameterMapper;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Builds the API collection value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function mapCollection(PersistenceCollection $collection)
    {
        $handler = $this->persistenceHandler->getCollectionHandler();

        $persistenceItems = $handler->loadCollectionItems($collection);

        $items = array();
        foreach ($persistenceItems as $persistenceItem) {
            $items[] = $this->mapItem($persistenceItem);
        }

        $query = null;
        $type = Collection::TYPE_MANUAL;

        try {
            $query = $this->mapQuery(
                $handler->loadCollectionQuery($collection)
            );

            $type = Collection::TYPE_DYNAMIC;
        } catch (NotFoundException $e) {
        }

        $collectionData = array(
            'id' => $collection->id,
            'status' => $collection->status,
            'type' => $type,
            'items' => $items,
            'query' => $query,
            'published' => $collection->status === Value::STATUS_PUBLISHED,
        );

        return new Collection($collectionData);
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
        $itemData = array(
            'id' => $item->id,
            'status' => $item->status,
            'collectionId' => $item->collectionId,
            'position' => $item->position,
            'type' => $item->type,
            'valueId' => $item->valueId,
            'valueType' => $item->valueType,
            'published' => $item->status === Value::STATUS_PUBLISHED,
        );

        return new Item($itemData);
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
        $queryType = $this->queryTypeRegistry->getQueryType(
            $query->type
        );

        $queryData = array(
            'id' => $query->id,
            'status' => $query->status,
            'collectionId' => $query->collectionId,
            'queryType' => $queryType,
            'published' => $query->status === Value::STATUS_PUBLISHED,
            'parameters' => $this->parameterMapper->mapParameters(
                $queryType,
                $query->parameters
            ),
        );

        return new Query($queryData);
    }
}
