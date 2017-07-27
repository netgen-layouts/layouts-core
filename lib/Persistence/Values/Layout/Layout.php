<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Persistence\Values\Value;

class Layout extends Value
{
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
     * Returns the main locale of this layout.
     *
     * @var string
     */
    public $mainLocale;

    /**
     * Returns the list of all locales available in this layout.
     *
     * @var string[]
     */
    public $availableLocales;

    /**
     * Layout status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
