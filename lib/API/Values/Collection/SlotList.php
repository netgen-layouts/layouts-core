<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\LazyCollection;
use Ramsey\Uuid\UuidInterface;

use function array_map;

/**
 * @extends \Netgen\Layouts\API\Values\LazyCollection<int, \Netgen\Layouts\API\Values\Collection\Slot>
 */
final class SlotList extends LazyCollection
{
    /**
     * @return \Netgen\Layouts\API\Values\Collection\Slot[]
     */
    public function getSlots(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getSlotIds(): array
    {
        return array_map(
            static fn (Slot $slot): UuidInterface => $slot->id,
            $this->getSlots(),
        );
    }
}
