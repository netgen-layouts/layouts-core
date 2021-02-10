<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Slot extends Value
{
    use HydratorTrait;

    /**
     * Slot ID.
     */
    public int $id;

    /**
     * Slot UUID.
     */
    public string $uuid;

    /**
     * ID of the collection to which this slot belongs.
     */
    public int $collectionId;

    /**
     * UUID of the collection to which this slot belongs.
     */
    public string $collectionUuid;

    /**
     * Position of the slot within the collection.
     */
    public int $position;

    /**
     * View type which will be used to render the item located at this slot.
     */
    public ?string $viewType;
}
