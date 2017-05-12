<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class QueryCreateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $parameters;
}
