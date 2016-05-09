<?php

namespace Netgen\BlockManager\Persistence\Values\Page;

use Netgen\BlockManager\API\Values\Value;

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
    public $status;

    /**
     * Collection ID.
     *
     * @var int|string
     */
    public $collectionId;

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
