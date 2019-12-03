<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

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
                static function (Slot $slot): bool {
                    return true;
                }
            )
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
            static function (Slot $slot): UuidInterface {
                return $slot->getId();
            },
            $this->getSlots()
        );
    }
}
