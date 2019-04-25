<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;

final class ItemList extends ArrayCollection
{
    public function __construct(array $items = [])
    {
        parent::__construct(
            array_filter(
                $items,
                static function (Item $item): bool {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\Collection\Item[]
     */
    public function getItems(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getItemIds(): array
    {
        return array_map(
            static function (Item $item) {
                return $item->getId();
            },
            $this->getItems()
        );
    }
}
