<?php

namespace Netgen\BlockManager\API\Values\Block;

interface CollectionReference
{
    /**
     * Returns the block to which the collection is attached.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function getBlock();

    /**
     * Returns the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function getCollection();

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
