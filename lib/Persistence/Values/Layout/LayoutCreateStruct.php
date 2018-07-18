<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Utils\HydratorTrait;

final class LayoutCreateStruct
{
    use HydratorTrait;

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
     * @var string
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
