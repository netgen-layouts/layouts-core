<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\CollectionReference as APICollectionReference;
use Netgen\BlockManager\ValueObject;

class CollectionReference extends ValueObject implements APICollectionReference
{
    /**
     * @var int
     */
    protected $blockId;

    /**
     * @var int
     */
    protected $blockStatus;

    /**
     * @var int
     */
    protected $collectionId;

    /**
     * @var int
     */
    protected $collectionStatus;

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
     * Returns the block ID to which the collection is attached.
     *
     * @return int|string
     */
    public function getBlockId()
    {
        return $this->blockId;
    }

    /**
     * Returns the block status to which the collection is attached.
     *
     * @return int
     */
    public function getBlockStatus()
    {
        return $this->blockStatus;
    }

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Returns the collection status.
     *
     * @return int|string
     */
    public function getCollectionStatus()
    {
        return $this->collectionStatus;
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
