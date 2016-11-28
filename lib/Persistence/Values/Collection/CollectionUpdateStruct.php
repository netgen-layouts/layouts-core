<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class CollectionUpdateStruct extends ValueObject
{
    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $name;
}
