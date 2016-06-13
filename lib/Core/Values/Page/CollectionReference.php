<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\CollectionReference as APICollectionReference;
use Netgen\BlockManager\ValueObject;

class CollectionReference extends ValueObject implements APICollectionReference
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Block
     */
    protected $block;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Collection
     */
    protected $collection;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

    /**
     * Returns the block to which the collection is attached.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Returns the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Returns the configuration identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the configuration offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Returns the configuration limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
