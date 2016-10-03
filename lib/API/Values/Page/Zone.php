<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;
use IteratorAggregate;
use Countable;

interface Zone extends Value, IteratorAggregate, Countable
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
     * Returns the linked zone.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function getLinkedZone();

    /**
     * Returns zone blocks.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function getBlocks();
}
