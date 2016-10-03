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
     * @var \Netgen\BlockManager\API\Values\Page\Zone
     */
    protected $linkedZone;

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
     * Returns the linked zone.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getLinkedZone()
    {
        return $this->linkedZone;
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

    /**
     * Returns the count of blocks in the zone.
     *
     * If linked zone is specified, it's count is returned.
     *
     * @return int
     */
    public function count()
    {
        if ($this->linkedZone instanceof self) {
            return $this->linkedZone->count();
        }

        return count($this->blocks);
    }
}
