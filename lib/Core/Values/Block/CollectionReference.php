<?php

namespace Netgen\BlockManager\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\CollectionReference as APICollectionReference;
use Netgen\BlockManager\ValueObject;

final class CollectionReference extends ValueObject implements APICollectionReference
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    protected $collection;

    /**
     * @var string
     */
    protected $identifier;

    public function getCollection()
    {
        return $this->collection;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
}
