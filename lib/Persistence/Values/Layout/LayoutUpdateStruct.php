<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Utils\HydratorTrait;

final class LayoutUpdateStruct
{
    use HydratorTrait;

    /**
     * New layout name.
     */
    public ?string $name = null;

    /**
     * Modification date of the layout.
     */
    public ?int $modified = null;

    /**
     * New human readable description of the layout.
     */
    public ?string $description = null;
}
