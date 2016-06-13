<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;

interface CollectionReference extends Value
{
    /**
     * Returns the block to which the collection is attached.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
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
