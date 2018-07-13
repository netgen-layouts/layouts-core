<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Block;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Value;

final class CollectionReference extends Value
{
    use LazyPropertyTrait;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    private $collection;

    /**
     * @var string
     */
    private $identifier;

    public function getCollection(): Collection
    {
        return $this->getLazyProperty($this->collection);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
