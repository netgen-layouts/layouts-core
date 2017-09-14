<?php

namespace Netgen\BlockManager\API\Values\Block;

/**
 * Collection reference represents the link between the block and the collection.
 * While the collection itself does not have any kind of identifiers,
 * when linked to a block, the identifier in the link is used to reference
 * to a specific collection in the block.
 *
 * The offset and limit specified in the reference are used as the default offset
 * and limit when fetching the collection results.
 */
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
