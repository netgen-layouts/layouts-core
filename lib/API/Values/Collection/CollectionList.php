<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;

final class CollectionList extends ArrayCollection
{
    public function __construct(array $collections = [])
    {
        parent::__construct(
            array_filter(
                $collections,
                function (Collection $collection) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    public function getCollections(): array
    {
        return $this->toArray();
    }

    /**
     * @return int[]|string[]
     */
    public function getCollectionIds(): array
    {
        return array_map(
            function (Collection $collection) {
                return $collection->getId();
            },
            $this->getCollections()
        );
    }
}
