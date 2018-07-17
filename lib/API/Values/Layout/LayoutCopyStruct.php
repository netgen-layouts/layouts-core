<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Layout;

final class LayoutCopyStruct
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
     * @var string|null
     */
    public $description;
}
