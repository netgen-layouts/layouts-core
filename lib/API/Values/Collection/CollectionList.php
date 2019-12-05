<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<string, \Netgen\Layouts\API\Values\Collection\Collection>
 */
final class CollectionList extends ArrayCollection
{
    /**
     * @param array<string, \Netgen\Layouts\API\Values\Collection\Collection> $collections
     */
    public function __construct(array $collections = [])
    {
        parent::__construct(
            array_filter(
                $collections,
                static function (Collection $collection): bool {
                    return true;
                }
            )
        );
    }

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
                static function (Collection $collection): UuidInterface {
                    return $collection->getId();
                },
                $this->getCollections()
            )
        );
    }
}
