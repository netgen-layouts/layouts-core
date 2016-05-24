<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class QueryCreateStruct extends ValueObject
{
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
