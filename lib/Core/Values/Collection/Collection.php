<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;

class Collection extends APICollection
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    protected $manualItems;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    protected $overrideItems;

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query[]
     */
    protected $queries;

    /**
     * Returns the collection ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the collection status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the collection type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the collection name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the list of items manually added to the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getManualItems()
    {
        return $this->manualItems;
    }

    /**
     * Returns the list of items that override the item present at a specific slot.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item[]
     */
    public function getOverrideItems()
    {
        return $this->overrideItems;
    }

    /**
     * Returns the list of query configurations in the collection.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query[]
     */
    public function getQueries()
    {
        return $this->queries;
    }
}
