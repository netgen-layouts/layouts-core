<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Utils\HydratorTrait;

final class LayoutUpdateStruct
{
    use HydratorTrait;

    /**
     * New layout name.
     *
     * @var string|null
     */
    public $name;

    /**
     * Modification date of the layout.
     *
     * @var int|null
     */
    public $modified;

    /**
     * New human readable description of the layout.
     *
     * @var string|null
     */
    public $description;
}
