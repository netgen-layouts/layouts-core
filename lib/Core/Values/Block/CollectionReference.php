<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\CollectionReference as APICollectionReference;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Value;

final class CollectionReference extends Value implements APICollectionReference
{
    use LazyPropertyTrait;

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
        return $this->getLazyProperty($this->collection);
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
}
