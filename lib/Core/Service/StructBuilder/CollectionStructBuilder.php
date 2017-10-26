<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryTypeInterface;

final class CollectionStructBuilder
{
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
            array(
                'queryCreateStruct' => $queryCreateStruct,
            )
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
            $collectionUpdateStruct->limit = $collection->getLimit();
            if ($collectionUpdateStruct->limit === null) {
                $collectionUpdateStruct->limit = 0;
            }
        }

        return $collectionUpdateStruct;
    }

    /**
     * Creates a new item create struct from provided values.
     *
     * @param int $type
     * @param int|string $valueId
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct
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
     * Creates a new query create struct from provided query type.
     *
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType)
    {
        $queryCreateStruct = new QueryCreateStruct(
            array(
                'queryType' => $queryType,
            )
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
