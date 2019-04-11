<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Layout extends Value
{
    use HydratorTrait;

    /**
     * Layout ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Layout type.
     *
     * @var string
     */
    public $type;

    /**
     * Human readable layout name.
     *
     * @var string
     */
    public $name;

    /**
     * Human readable description of the layout.
     *
     * @var string
     */
    public $description;

    /**
     * Flag indicating if this layout is shared.
     *
     * @var bool
     */
    public $shared;

    /**
     * Timestamp when the layout was created.
     *
     * @var int
     */
    public $created;

    /**
     * Timestamp when the layout was last updated.
     *
     * @var int
     */
    public $modified;

    /**
     * Main locale of this layout.
     *
     * @var string
     */
    public $mainLocale;

    /**
     * List of all locales available in this layout.
     *
     * @var string[]
     */
    public $availableLocales;
}
