<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\ValueObject;

class LayoutCreateStruct extends ValueObject
{
    /**
     * @var \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    public $layoutType;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $shared;
}