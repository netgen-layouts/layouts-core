<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\ValueObject;

class Zone extends ValueObject implements APIZone
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $linkedLayoutId;

    /**
     * @var string
     */
    protected $linkedZoneIdentifier;

    /**
     * @var array
     */
    protected $blocks = array();

    /**
     * Returns zone identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the layout ID to which this zone belongs.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns the status of the zone.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the linked layout ID.
     *
     * @return int
     */
    public function getLinkedLayoutId()
    {
        return $this->linkedLayoutId;
    }

    /**
     * Returns the linked zone identifier.
     *
     * @return string
     */
    public function getLinkedZoneIdentifier()
    {
        return $this->linkedZoneIdentifier;
    }

    /**
     * Returns zone blocks.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}
