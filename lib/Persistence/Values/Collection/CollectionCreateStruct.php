<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class CollectionCreateStruct
{
    use HydratorTrait;

    /**
     * Status of the new collection.
     */
    public int $status;

    /**
     * Starting offset for the collection results.
     */
    public int $offset;

    /**
     * Starting limit for the collection results.
     */
    public ?int $limit;

    /**
     * Flag indicating if the collection will be always available.
     */
    public bool $alwaysAvailable;

    /**
     * Flag indicating if the collection will be translatable.
     */
    public bool $isTranslatable;

    /**
     * Main locale of the new collection.
     */
    public string $mainLocale;
}
