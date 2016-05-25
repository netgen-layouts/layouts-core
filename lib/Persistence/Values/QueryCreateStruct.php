<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class QueryCreateStruct extends ValueObject
{
    /**
     * @var int|string
     */
    public $collectionId;

    /**
     * @var int
     */
    public $position;

    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $identifier;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $parameters;
}
