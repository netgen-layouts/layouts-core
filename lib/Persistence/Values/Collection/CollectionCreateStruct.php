<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class CollectionCreateStruct extends ValueObject
{
    /**
     * @var int
     */
    public $type;

    /**
     * @var int
     */
    public $status;
}
