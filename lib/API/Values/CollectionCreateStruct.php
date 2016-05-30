<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\ValueObject;

class CollectionCreateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $type = Collection::TYPE_MANUAL;
}
