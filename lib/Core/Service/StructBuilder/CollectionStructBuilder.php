<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryTypeInterface;

class CollectionStructBuilder
{
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

        $queryCreateStruct->fill($queryType);

        return $queryCreateStruct;
    }

    /**
     * Creates a new query update struct.
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

        $queryUpdateStruct->fillFromValue($query->getQueryType(), $query);

        return $queryUpdateStruct;
    }
}
