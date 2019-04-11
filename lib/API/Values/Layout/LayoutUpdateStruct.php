<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

final class LayoutUpdateStruct
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
