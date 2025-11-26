<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\LazyCollection;
use Ramsey\Uuid\UuidInterface;

use function array_map;
use function array_values;

/**
 * @extends \Netgen\Layouts\API\Values\LazyCollection<string, \Netgen\Layouts\API\Values\Collection\Collection>
 */
final class CollectionList extends LazyCollection
{
    /**
     * @return array<string, \Netgen\Layouts\API\Values\Collection\Collection>
     */
    public function getCollections(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getCollectionIds(): array
    {
        return array_values(
            array_map(
                static fn (Collection $collection): UuidInterface => $collection->id,
                $this->getCollections(),
            ),
        );
    }
}
