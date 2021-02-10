<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Block;

use Netgen\Layouts\Utils\HydratorTrait;

final class CollectionReference
{
    use HydratorTrait;

    /**
     * Block ID.
     */
    public int $blockId;

    /**
     * Block status.
     */
    public int $blockStatus;

    /**
     * Collection ID.
     */
    public int $collectionId;

    /**
     * Collection status.
     */
    public int $collectionStatus;

    /**
     * Identifier of the collection reference.
     */
    public string $identifier;
}
