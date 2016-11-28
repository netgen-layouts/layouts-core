<?php

namespace Netgen\BlockManager\API\Values\Page;

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
     * @var bool
     */
    public $shared;
}
