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
     *
     * @var string|null
     */
    public $uuid;

    /**
     * Identifier of the layout type for the new layout.
     *
     * @var string
     */
    public $type;

    /**
     * Name of the new layout.
     *
     * @var string
     */
    public $name;

    /**
     * Human readable description of the new layout.
     *
     * @var string|null
     */
    public $description;

    /**
     * Status of the new layout. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;

    /**
     * Flag indicating if the layout will be shared.
     *
     * @var bool
     */
    public $shared;

    /**
     * Main locale of the new layout.
     *
     * @var string
     */
    public $mainLocale;
}
