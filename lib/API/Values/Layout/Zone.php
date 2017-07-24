<?php

namespace Netgen\BlockManager\API\Values\Layout;

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
     * Returns if the zone is published.
     *
     * @return bool
     */
    public function isPublished();

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
