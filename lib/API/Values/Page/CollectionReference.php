<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;

abstract class CollectionReference extends Value
{
    /**
     * Returns the block ID to which the collection is attached.
     *
     * @return int|string
     */
    abstract public function getBlockId();

    /**
     * Returns the block status to which the collection is attached.
     *
     * @return int
     */
    abstract public function getStatus();

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    abstract public function getCollectionId();

    /**
     * Returns the configuration identifier.
     *
     * @return string
     */
    abstract public function getIdentifier();

    /**
     * Returns the configuration offset.
     *
     * @return int
     */
    abstract public function getOffset();

    /**
     * Returns the configuration limit.
     *
     * @return int
     */
    abstract public function getLimit();
}
