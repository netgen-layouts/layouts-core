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
     * @var int
     */
    public $id;

    /**
     * Query UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * ID of the collection to which this query belongs.
     *
     * @var int
     */
    public $collectionId;

    /**
     * UUID of the collection to which this query belongs.
     *
     * @var string
     */
    public $collectionUuid;

    /**
     * Query type.
     *
     * @var string
     */
    public $type;

    /**
     * Query parameters.
     *
     * @var array<string, array<string, mixed>>
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
