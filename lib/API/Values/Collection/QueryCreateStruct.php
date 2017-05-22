<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterStruct;

class QueryCreateStruct extends ParameterStruct
{
    /**
     * Query type for which the new query will be created.
     *
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public $queryType;
}
