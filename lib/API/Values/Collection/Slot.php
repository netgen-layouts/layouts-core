<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Slot implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $collectionId;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $viewType;

    /**
     * Returns the slot UUID.
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns the UUID of the collection to which the slot belongs.
     */
    public function getCollectionId(): UuidInterface
    {
        return $this->collectionId;
    }

    /**
     * Returns the slot position within the collection.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Returns the view type which will be used to render the item located at this slot.
     *
     * @return string
     */
    public function getViewType(): string
    {
        return $this->viewType;
    }
}
