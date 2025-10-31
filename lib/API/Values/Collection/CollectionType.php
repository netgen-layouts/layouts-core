<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

enum CollectionType: int
{
    /**
     * Denotes that the collection is manual, i.e., does not have a query.
     */
    case Manual = 0;

    /**
     * Denotes that the collection is dynamic, i.e., that it has a query.
     */
    case Dynamic = 1;
}
