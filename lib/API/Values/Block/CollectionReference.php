<?php

namespace Netgen\BlockManager\API\Values\Block;

interface CollectionReference
{
    /**
     * Returns the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function getCollection();

    /**
     * Returns the collection identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the collection offset.
     *
     * @return int
     */
    public function getOffset();

    /**
     * Returns the collection limit.
     *
     * @return int
     */
    public function getLimit();
}
