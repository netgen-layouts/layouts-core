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
     */
    public int $id;

    /**
     * Query UUID.
     */
    public string $uuid;

    /**
     * ID of the collection to which this query belongs.
     */
    public int $collectionId;

    /**
     * UUID of the collection to which this query belongs.
     */
    public string $collectionUuid;

    /**
     * Query type.
     */
    public string $type;

    /**
     * Query parameters.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $parameters;

    /**
     * Returns if the query is translatable.
     */
    public bool $isTranslatable;

    /**
     * Returns the main locale of this query.
     */
    public string $mainLocale;

    /**
     * Returns the list of all locales available in this query.
     *
     * @var string[]
     */
    public array $availableLocales;

    /**
     * Returns if main locale of this query will be always available.
     */
    public bool $alwaysAvailable;
}
