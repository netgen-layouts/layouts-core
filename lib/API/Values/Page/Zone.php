<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;

abstract class Zone extends Value
{
    /**
     * Returns zone identifier.
     *
     * @return string
     */
    abstract public function getIdentifier();

    /**
     * Returns the layout ID to which this zone belongs.
     *
     * @return int|string
     */
    abstract public function getLayoutId();

    /**
     * Returns the status of the zone.
     *
     * @return string
     */
    abstract public function getStatus();

    /**
     * Returns zone blocks.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    abstract public function getBlocks();
}
