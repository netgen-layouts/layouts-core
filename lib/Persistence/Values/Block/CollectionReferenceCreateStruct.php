<?php

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\ValueObject;

final class CollectionReferenceCreateStruct extends ValueObject
{
    /**
     * Identifier of the reference.
     *
     * @var string
     */
    public $identifier;

    /**
     * The collection to link to.
     *
     * @var \Netgen\BlockManager\Persistence\Values\Collection\Collection
     */
    public $collection;
}
