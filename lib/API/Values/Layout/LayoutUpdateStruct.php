<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\Value;

final class LayoutUpdateStruct extends Value
{
    /**
     * New human readable name of the layout.
     *
     * @var string|null
     */
    public $name;

    /**
     * New description of the layout.
     *
     * @var string|null
     */
    public $description;
}
