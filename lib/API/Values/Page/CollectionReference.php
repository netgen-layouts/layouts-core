<?php

namespace Netgen\BlockManager\API\Values\Page;

interface CollectionReference
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
    public function getStatus();

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    public function getCollectionId();

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
