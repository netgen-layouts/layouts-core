<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterStruct;

class QueryCreateStruct extends ParameterStruct
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public $queryType;
}
