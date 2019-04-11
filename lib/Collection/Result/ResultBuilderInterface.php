<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Collection;

/**
 * Result builder is a service responsible for generating a result of a collection.
 */
interface ResultBuilderInterface
{
    /**
     * Builds the result set from the provided collection.
     */
    public function build(Collection $collection, int $offset = 0, ?int $limit = null, int $flags = 0): ResultSet;
}
