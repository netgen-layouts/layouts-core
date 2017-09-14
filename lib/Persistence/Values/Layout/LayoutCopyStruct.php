<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\ValueObject;

class LayoutCopyStruct extends ValueObject
{
    /**
     * Name of the copied layout.
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
