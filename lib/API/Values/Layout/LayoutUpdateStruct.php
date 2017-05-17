<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\ValueObject;

class LayoutUpdateStruct extends ValueObject
{
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
}
