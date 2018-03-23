<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\Value;

final class LayoutCopyStruct extends Value
{
    /**
     * Human readable name of the copied layout.
     *
     * Required.
     *
     * @var string
     */
    public $name;

    /**
     * Description of the copied layout.
     *
     * @var string
     */
    public $description;
}
