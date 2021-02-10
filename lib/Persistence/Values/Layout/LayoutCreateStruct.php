<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Utils\HydratorTrait;

final class LayoutCreateStruct
{
    use HydratorTrait;

    /**
     * Layout UUID. If specified, layout will be created with this UUID if not
     * already taken by an existing layout.
     */
    public ?string $uuid;

    /**
     * Identifier of the layout type for the new layout.
     */
    public string $type;

    /**
     * Name of the new layout.
     */
    public string $name;

    /**
     * Human readable description of the new layout.
     */
    public string $description;

    /**
     * Status of the new layout. One of self::STATUS_* flags.
     */
    public int $status;

    /**
     * Flag indicating if the layout will be shared.
     */
    public bool $shared;

    /**
     * Main locale of the new layout.
     */
    public string $mainLocale;
}
