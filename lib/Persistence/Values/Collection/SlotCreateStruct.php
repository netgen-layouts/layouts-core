<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class SlotCreateStruct
{
    use HydratorTrait;

    /**
     * Position of the new slot in the collection.
     */
    public int $position;

    /**
     * View type which will be used to render the item located at the new slot.
     */
    public ?string $viewType;
}
