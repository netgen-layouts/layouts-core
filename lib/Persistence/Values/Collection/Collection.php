<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Value;

final class Collection extends Value
{
    /**
     * Collection ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * Returns if the collection is translatable.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * Returns the main locale of this collection.
     *
     * @var string
     */
    public $mainLocale;

    /**
     * Returns the list of all locales available in this collection.
     *
     * @var string[]
     */
    public $availableLocales;

    /**
     * Returns if main locale of this collection will be always available.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * Collection status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
