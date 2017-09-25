<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Value;

final class Query extends Value
{
    /**
     * Query ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * ID of the collection to which this query belongs.
     *
     * @var int|string
     */
    public $collectionId;

    /**
     * Query type.
     *
     * @var string
     */
    public $type;

    /**
     * Query parameters.
     *
     * @var array
     */
    public $parameters;

    /**
     * Returns if the query is translatable.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * Returns the main locale of this query.
     *
     * @var string
     */
    public $mainLocale;

    /**
     * Returns the list of all locales available in this query.
     *
     * @var string[]
     */
    public $availableLocales;

    /**
     * Returns if main locale of this query will be always available.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * Query status. One of self::STATUS_* flags.
     *
     * @var int
     */
    public $status;
}
