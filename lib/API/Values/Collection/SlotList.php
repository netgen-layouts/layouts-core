<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_map;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\Collection\Slot>
 */
final class SlotList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\Collection\Slot[] $slots
     */
    public function __construct(array $slots = [])
    {
        parent::__construct(
            array_filter(
                $slots,
                static fn (Slot $slot): bool => true,
            ),
        );
    }

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
            static fn (Slot $slot): UuidInterface => $slot->getId(),
            $this->getSlots(),
        );
    }
}
