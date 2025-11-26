<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\LazyCollection;
use Ramsey\Uuid\UuidInterface;

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
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getItemIds(): array
    {
        return array_map(
            static fn (Item $item): UuidInterface => $item->id,
            $this->getItems(),
        );
    }
}
