<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Persistence\Values\Value;

final class CollectionReference extends Value
{
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
     * @var int|string
     */
    public $collectionStatus;

    /**
     * Identifier of the collection reference.
     *
     * @var string
     */
    public $identifier;

    /**
     * The starting offset for the collection results.
     *
     * @var int
     */
    public $offset;

    /**
     * The starting limit for the collection results.
     *
     * @var int
     */
    public $limit;
}
