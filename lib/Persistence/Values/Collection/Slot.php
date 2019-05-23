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
     *
     * @var int
     */
    public $id;

    /**
     * Slot UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * ID of the collection to which this slot belongs.
     *
     * @var int
     */
    public $collectionId;

    /**
     * UUID of the collection to which this slot belongs.
     *
     * @var string
     */
    public $collectionUuid;

    /**
     * Position of the slot within the collection.
     *
     * @var int
     */
    public $position;

    /**
     * View type which will be used to render the item located at this slot.
     *
     * @var string
     */
    public $viewType;
}
