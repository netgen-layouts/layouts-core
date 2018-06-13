<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;

final class CollectionStructBuilder
{
    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder
     */
    private $configStructBuilder;

    public function __construct(ConfigStructBuilder $configStructBuilder)
    {
        $this->configStructBuilder = $configStructBuilder;
    }

    /**
     * Creates a new collection create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct $queryCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct
     */
    public function newCollectionCreateStruct(QueryCreateStruct $queryCreateStruct = null)
    {
        return new CollectionCreateStruct(
            [
                'queryCreateStruct' => $queryCreateStruct,
            ]
        );
    }

    /**
     * Creates a new collection update struct.
     *
     * If collection is provided, initial data is copied from the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct
     */
    public function newCollectionUpdateStruct(Collection $collection = null)
    {
        $collectionUpdateStruct = new CollectionUpdateStruct();

        if ($collection !== null) {
            $collectionUpdateStruct->offset = $collection->getOffset();
            $collectionUpdateStruct->limit = $collection->getLimit() ?? 0;
        }

        return $collectionUpdateStruct;
    }

    /**
     * Creates a new item create struct from provided values.
     *
     * @param \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface $itemDefinition
     * @param int $type
     * @param int|string $value
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct
     */
    public function newItemCreateStruct(ItemDefinitionInterface $itemDefinition, $type, $value)
    {
        return new ItemCreateStruct(
            [
                'definition' => $itemDefinition,
                'type' => $type,
                'value' => $value,
            ]
        );
    }

    /**
     * Creates a new item update struct.
     *
     * If item is provided, initial data is copied from the item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemUpdateStruct
     */
    public function newItemUpdateStruct(Item $item = null)
    {
        $itemUpdateStruct = new ItemUpdateStruct();

        if (!$item instanceof Item) {
            return $itemUpdateStruct;
        }

        $this->configStructBuilder->buildConfigUpdateStructs($item, $itemUpdateStruct);

        return $itemUpdateStruct;
    }

    /**
     * Creates a new query create struct from provided query type.
     *
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface $queryType
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType)
    {
        $queryCreateStruct = new QueryCreateStruct(
            [
                'queryType' => $queryType,
            ]
        );

        $queryCreateStruct->fillParameters($queryType);

        return $queryCreateStruct;
    }

    /**
     * Creates a new query update struct for provided locale.
     *
     * If query is provided, initial data is copied from the query.
     *
     * @param string $locale
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct
     */
    public function newQueryUpdateStruct($locale, Query $query = null)
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->locale = $locale;

        if (!$query instanceof Query) {
            return $queryUpdateStruct;
        }

        $queryUpdateStruct->fillParametersFromQuery($query);

        return $queryUpdateStruct;
    }
}
