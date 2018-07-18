<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Utils\HydratorTrait;

final class CollectionReference
{
    use HydratorTrait;

    /**
     * Block ID.
     *
     * @var int|string
     */
    public $blockId;

    /**
     * Block status.
     *
     * @var int
     */
    public $blockStatus;

    /**
     * Collection ID.
     *
     * @var int|string
     */
    public $collectionId;

    /**
     * Collection status.
     *
     * @var int
     */
    public $collectionStatus;

    /**
     * Identifier of the collection reference.
     *
     * @var string
     */
    public $identifier;
}
