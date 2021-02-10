<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

final class LayoutCopyStruct
{
    /**
     * Human readable name of the copied layout.
     *
     * Required.
     */
    public string $name;

    /**
     * Description of the copied layout.
     */
    public ?string $description = null;
}
