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
     */
    public int $id;

    /**
     * Collection UUID.
     */
    public string $uuid;

    /**
     * ID of the block where this collection is located.
     */
    public int $blockId;

    /**
     * UUID of the block where this collection is located.
     */
    public string $blockUuid;

    /**
     * The starting offset for the collection results.
     */
    public int $offset;

    /**
     * The starting limit for the collection results.
     */
    public ?int $limit;

    /**
     * Returns if the collection is translatable.
     */
    public bool $isTranslatable;

    /**
     * Returns the main locale of this collection.
     */
    public string $mainLocale;

    /**
     * Returns the list of all locales available in this collection.
     *
     * @var string[]
     */
    public array $availableLocales;

    /**
     * Returns if main locale of this collection will be always available.
     */
    public bool $alwaysAvailable;
}
