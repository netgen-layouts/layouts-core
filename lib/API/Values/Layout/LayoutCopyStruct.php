<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\ValueObject;

final class LayoutCopyStruct extends ValueObject
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
