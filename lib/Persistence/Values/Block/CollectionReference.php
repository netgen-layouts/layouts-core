<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Persistence\Values\Value;

class CollectionReference extends Value
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
     * Collection ID.
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
     * The offset of the reference.
     *
     * @var int
     */
    public $offset;

    /**
     * The limit of the reference.
     *
     * @var int
     */
    public $limit;
}
