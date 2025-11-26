<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Slot implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) UuidInterface $id;

    public private(set) Status $status;

    /**
     * Returns the UUID of the collection to which the slot belongs.
     */
    public private(set) UuidInterface $collectionId;

    /**
     * Returns the slot position within the collection.
     */
    public private(set) int $position;

    /**
     * Returns the view type which will be used to render the item located at this slot.
     */
    public private(set) ?string $viewType;

    /**
     * Returns if the slot is considered empty. Empty slots can be safely deleted as they do not
     * contain any relevant data.
     */
    public bool $isEmpty {
        get => $this->viewType === null;
    }
}
