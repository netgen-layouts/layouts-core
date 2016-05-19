<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;

interface CollectionReference extends Value
{
    /**
     * Returns the block ID to which the collection is attached.
     *
     * @return int|string
     */
    public function getBlockId();

    /**
     * Returns the block status to which the collection is attached.
     *
     * @return int
     */
    public function getBlockStatus();

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    public function getCollectionId();

    /**
     * Returns the collection status.
     *
     * @return int|string
     */
    public function getCollectionStatus();

    /**
     * Returns the configuration identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the configuration offset.
     *
     * @return int
     */
    public function getOffset();

    /**
     * Returns the configuration limit.
     *
     * @return int
     */
    public function getLimit();
}
