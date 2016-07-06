<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\ValueObject;

class Layout extends ValueObject implements APILayout
{
    /**
     * @var int|string
     */
    protected $id;

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
     * @var bool
     */
    protected $shared;

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
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns if the layout is shared.
     * 
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
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

    /**
     * Returns the specified zone or null if zone does not exist.
     *
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getZone($zoneIdentifier)
    {
        return isset($this->zones[$zoneIdentifier]) ? $this->zones[$zoneIdentifier] : null;
    }

    /**
     * Returns if layout has a specified zone.
     *
     * @param string $zoneIdentifier
     *
     * @return bool
     */
    public function hasZone($zoneIdentifier)
    {
        return isset($this->zones[$zoneIdentifier]);
    }
}
