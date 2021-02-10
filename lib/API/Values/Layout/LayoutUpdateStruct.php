<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

final class LayoutUpdateStruct
{
    /**
     * New human readable name of the layout.
     */
    public ?string $name = null;

    /**
     * New description of the layout.
     */
    public ?string $description = null;
}
