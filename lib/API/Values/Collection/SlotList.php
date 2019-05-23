<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Doctrine\Common\Collections\ArrayCollection;

final class SlotList extends ArrayCollection
{
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
            static function (Slot $slot) {
                return $slot->getId();
            },
            $this->getSlots()
        );
    }
}
