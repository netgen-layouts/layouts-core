<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Ramsey\Uuid\UuidInterface;

final class LayoutCreateStruct
{
    /**
     * Layout UUID. If specified, layout will be created with this UUID if not
     * already taken by an existing layout.
     */
    public ?UuidInterface $uuid = null;

    /**
     * Layout type from which the new layout will be created.
     *
     * Required.
     */
    public LayoutTypeInterface $layoutType;

    /**
     * Human readable name of the layout.
     *
     * Required.
     */
    public string $name;

    /**
     * Description of the layout.
     */
    public ?string $description = '';

    /**
     * Specifies if this layout will be shared or not.
     */
    public bool $shared = false;

    /**
     * Specifies the main locale of the layout.
     *
     * Required.
     */
    public string $mainLocale;
}
