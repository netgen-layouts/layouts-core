<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

interface EntityLoaderInterface
{
    /**
     * Loads the entities for provided UUIDs.
     *
     * @param string[] $entityIds
     *
     * @return iterable<\Netgen\Layouts\API\Values\Value>
     */
    public function loadEntities(array $entityIds): iterable;
}
