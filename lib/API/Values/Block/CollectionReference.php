<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Collection\Collection;

/**
 * Collection reference represents the link between the block and the collection.
 * While the collection itself does not have any kind of identifiers,
 * when linked to a block, the identifier in the link is used to reference
 * to a specific collection in the block.
 */
interface CollectionReference
{
    /**
     * Returns the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function getCollection(): Collection;

    /**
     * Returns the collection identifier.
     *
     * @return string
     */
    public function getIdentifier(): string;
}
