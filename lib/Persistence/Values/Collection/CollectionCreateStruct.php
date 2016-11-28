<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

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
    public $type;

    /**
     * @var bool
     */
    public $shared;

    /**
     * @var int
     */
    public $status;
}
