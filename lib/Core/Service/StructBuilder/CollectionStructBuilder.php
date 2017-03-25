<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryTypeInterface;

class CollectionStructBuilder
{
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
     * @return \Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct
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
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    public function newQueryCreateStruct(QueryTypeInterface $queryType, $identifier)
    {
        $queryCreateStruct = new QueryCreateStruct(
            array(
                'identifier' => $identifier,
                'queryType' => $queryType,
            )
        );

        $queryCreateStruct->fillValues($queryType);

        return $queryCreateStruct;
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
        $queryUpdateStruct = new QueryUpdateStruct();

        if (!$query instanceof Query) {
            return $queryUpdateStruct;
        }

        $queryUpdateStruct->identifier = $query->getIdentifier();

        $queryType = $query->getQueryType();
        $queryUpdateStruct->fillValues(
            $queryType,
            $query->getParameters(),
            false
        );

        return $queryUpdateStruct;
    }
}
