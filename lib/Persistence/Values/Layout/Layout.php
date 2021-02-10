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
     */
    public int $id;

    /**
     * Layout UUID.
     */
    public string $uuid;

    /**
     * Layout type.
     */
    public string $type;

    /**
     * Human readable layout name.
     */
    public string $name;

    /**
     * Human readable description of the layout.
     */
    public string $description;

    /**
     * Flag indicating if this layout is shared.
     */
    public bool $shared;

    /**
     * Timestamp when the layout was created.
     */
    public int $created;

    /**
     * Timestamp when the layout was last updated.
     */
    public int $modified;

    /**
     * Main locale of this layout.
     */
    public string $mainLocale;

    /**
     * List of all locales available in this layout.
     *
     * @var string[]
     */
    public array $availableLocales;
}
