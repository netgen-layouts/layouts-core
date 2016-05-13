<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Value;

class Layout extends Value implements APILayout
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $modified;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var \Netgen\BlockManager\API\Values\Page\Zone[]
     */
    protected $zones = array();

    /**
     * Returns the layout ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the parent layout ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Returns the layout type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the layout human readable name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns when was the layout created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Returns when was the layout last updated.
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Returns the status of the layout.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone[]
     */
    public function getZones()
    {
        return $this->zones;
    }
}
