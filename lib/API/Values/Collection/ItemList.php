<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\LazyCollection;
use Symfony\Component\Uid\Uuid;

use function array_map;

/**
 * @extends \Netgen\Layouts\API\Values\LazyCollection<int, \Netgen\Layouts\API\Values\Collection\Item>
 */
final class ItemList extends LazyCollection
{
    /**
     * @return \Netgen\Layouts\API\Values\Collection\Item[]
     */
    public function getItems(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Symfony\Component\Uid\Uuid[]
     */
    public function getItemIds(): array
    {
        return array_map(
            static fn (Item $item): Uuid => $item->id,
            $this->getItems(),
        );
    }
}
