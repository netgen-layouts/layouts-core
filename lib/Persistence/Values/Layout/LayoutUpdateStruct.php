<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Value;

final class LayoutUpdateStruct extends Value
{
    /**
     * New layout name.
     *
     * @var string|null
     */
    public $name;

    /**
     * Modification date of the layout.
     *
     * @var int|null
     */
    public $modified;

    /**
     * New human readable description of the layout.
     *
     * @var string|null
     */
    public $description;
}
