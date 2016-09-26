<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\ValueObject;

class CollectionCreateStruct extends ValueObject
{
    /**
     * @var int
     */
    public $type = Collection::TYPE_MANUAL;

    /**
     * @var bool
     */
    public $shared;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \Netgen\BlockManager\API\Values\ItemCreateStruct[]
     */
    public $itemCreateStructs = array();

    /**
     * @var \Netgen\BlockManager\API\Values\QueryCreateStruct[]
     */
    public $queryCreateStructs = array();
}
