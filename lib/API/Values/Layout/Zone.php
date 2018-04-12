<?php

namespace Netgen\BlockManager\API\Values\Layout;

use Netgen\BlockManager\API\Values\Value;

interface Zone extends Value
{
    /**
     * Returns the zone identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the ID of the layout to which this zone belongs.
     *
     * @return int|string
     */
    public function getLayoutId();

    /**
     * Returns if the zone has a linked zone.
     *
     * @return bool
     */
    public function hasLinkedZone();

    /**
     * Returns the linked zone.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function getLinkedZone();
}
