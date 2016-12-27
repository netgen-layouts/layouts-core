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
     * Returns if the zone is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns the linked zone.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getLinkedZone();
}
