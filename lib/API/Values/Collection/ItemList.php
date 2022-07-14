<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_map;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\Collection\Item>
 */
final class ItemList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\Collection\Item[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(
            array_filter(
                $items,
                static fn (Item $item): bool => true,
            ),
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
            static fn (Item $item): UuidInterface => $item->getId(),
            $this->getItems(),
        );
    }
}
