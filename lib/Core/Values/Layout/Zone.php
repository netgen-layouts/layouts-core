<?php

namespace Netgen\BlockManager\Core\Values\Layout;

use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
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
     * @var \Netgen\BlockManager\API\Values\Layout\Zone
     */
    protected $linkedZone;

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getLayoutId()
    {
        return $this->layoutId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function hasLinkedZone()
    {
        return $this->linkedZone instanceof APIZone;
    }

    public function getLinkedZone()
    {
        return $this->linkedZone;
    }
}
