<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;

interface Zone extends Value
{
    /**
     * Returns zone identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the layout ID to which this zone belongs.
     *
     * @return int|string
     */
    public function getLayoutId();

    /**
     * Returns the status of the zone.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns the linked layout ID.
     *
     * @return int
     */
    public function getLinkedLayoutId();

    /**
     * Returns the linked zone identifier.
     *
     * @return string
     */
    public function getLinkedZoneIdentifier();

    /**
     * Returns zone blocks.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function getBlocks();
}
