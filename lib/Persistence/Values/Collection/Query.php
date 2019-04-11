<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Query extends Value
{
    use HydratorTrait;

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
     * @var array[]
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
}
