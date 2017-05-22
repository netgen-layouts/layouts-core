<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\ValueObject;

class CollectionCreateStruct extends ValueObject
{
    /**
     * The type of the collection to create.
     *
     * One of Collection::TYPE_* constants.
     *
     * @var int
     */
    public $type = Collection::TYPE_MANUAL;

    /**
     * The list of items to create in the collection.
     *
     * @var \Netgen\BlockManager\API\Values\Collection\ItemCreateStruct[]
     */
    public $itemCreateStructs = array();

    /**
     * Query to create in the collection. Type must be set to TYPE_DYNAMIC.
     *
     * @var \Netgen\BlockManager\API\Values\Collection\QueryCreateStruct
     */
    public $queryCreateStruct;
}
