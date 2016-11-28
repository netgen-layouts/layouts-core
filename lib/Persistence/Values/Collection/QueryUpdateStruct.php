<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class QueryUpdateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var array
     */
    public $parameters;
}
