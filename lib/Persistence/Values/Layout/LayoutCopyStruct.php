<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Utils\HydratorTrait;

final class LayoutCopyStruct
{
    use HydratorTrait;

    /**
     * Name of the copied layout.
     */
    public string $name;

    /**
     * Description of the copied layout.
     */
    public ?string $description = null;
}
