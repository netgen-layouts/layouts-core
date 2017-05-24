<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\ValueObject;

class QueryCreateStruct extends ValueObject implements ParameterStruct
{
    use ParameterStructTrait;

    /**
     * Query type for which the new query will be created.
     *
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public $queryType;
}
