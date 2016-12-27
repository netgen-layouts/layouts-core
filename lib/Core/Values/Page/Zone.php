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
     * @var bool
     */
    protected $published;

    /**
     * @var \Netgen\BlockManager\API\Values\Page\Zone
     */
    protected $linkedZone;

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
     * Returns if the zone is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns the linked zone.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getLinkedZone()
    {
        return $this->linkedZone;
    }
}
