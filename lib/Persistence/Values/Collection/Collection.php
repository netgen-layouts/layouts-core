<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Collection extends Value
{
    use HydratorTrait;

    /**
     * Collection ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * The starting offset for the collection results.
     *
     * @var int
     */
    public $offset;

    /**
     * The starting limit for the collection results.
     *
     * @var int|null
     */
    public $limit;

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
}
