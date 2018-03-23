<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Value;

final class LayoutUpdateStruct extends Value
{
    /**
     * Layout modification timestamp.
     *
     * @var int
     */
    public $modified;

    /**
     * New layout name.
     *
     * @var string
     */
    public $name;

    /**
     * New human readable description of the layout.
     *
     * @var string
     */
    public $description;
}
