<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

final class CollectionUpdateStruct
{
    /**
     * The new offset for the collection.
     */
    public ?int $offset = null;

    /**
     * The new limit for the collection.
     *
     * Set to 0 to disable the limit.
     */
    public ?int $limit = null;
}
