<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

final class QueryCreateStruct extends ValueObject
{
    /**
     * Identifier of the type of new query.
     *
     * @var string
     */
    public $type;

    /**
     * Parameters for the new query.
     *
     * @var array
     */
    public $parameters;
}
