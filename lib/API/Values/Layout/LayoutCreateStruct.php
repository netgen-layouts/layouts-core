<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\ValueObject;

class LayoutCreateStruct extends ValueObject
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutType
     */
    public $layoutType;

    /**
     * @var string
     */
    public $name;

    /**
     * Human readable description of the layout.
     *
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $shared;
}
