<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

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
     * @var \Netgen\BlockManager\Persistence\Values\Page\ZoneCreateStruct[]
     */
    public $zoneCreateStructs;
}
