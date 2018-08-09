<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;

final class ItemList extends ArrayCollection
{
    public function __construct(array $items = [])
    {
        parent::__construct(
            array_filter(
                $items,
                function (Item $item) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getItems(): array
    {
        return $this->toArray();
    }

    /**
     * @return int[]|string[]
     */
    public function getItemIds(): array
    {
        return array_map(
            function (Item $item) {
                return $item->getId();
            },
            $this->getItems()
        );
    }
}
