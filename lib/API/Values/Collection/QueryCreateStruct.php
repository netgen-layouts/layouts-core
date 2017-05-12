<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterStruct;

class QueryCreateStruct extends ParameterStruct
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public $queryType;
}
