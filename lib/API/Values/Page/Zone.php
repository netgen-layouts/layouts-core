<?php

namespace Netgen\BlockManager\API\Values\Page;

interface Zone
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
     * @return string
     */
    public function getStatus();

    /**
     * Returns zone blocks.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function getBlocks();
}
