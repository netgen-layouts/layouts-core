<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

final class CollectionCreateStruct
{
    /**
     * The offset for the collection.
     */
    public int $offset = 0;

    /**
     * The limit for the collection.
     */
    public ?int $limit = null;

    /**
     * If set, the collection will have a query created from this query struct.
     */
    public ?QueryCreateStruct $queryCreateStruct = null;
}
