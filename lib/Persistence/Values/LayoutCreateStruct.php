<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class LayoutCreateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $status;

    /**
     * @var bool
     */
    public $shared;

    /**
     * @var \Netgen\BlockManager\Persistence\Values\ZoneCreateStruct[]
     */
    public $zoneCreateStructs;
}
