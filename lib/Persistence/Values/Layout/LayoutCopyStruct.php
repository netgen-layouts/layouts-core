<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Value;

final class LayoutCopyStruct extends Value
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
     * @var string|null
     */
    public $description;
}
