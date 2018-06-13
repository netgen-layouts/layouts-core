<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Block;

use Netgen\BlockManager\Value;

final class CollectionReferenceCreateStruct extends Value
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
