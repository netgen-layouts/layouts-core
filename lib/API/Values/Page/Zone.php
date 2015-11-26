<?php

namespace Netgen\BlockManager\API\Values\Page;

interface Zone
{
    /**
     * Returns the zone ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the layout ID to which this zone belongs.
     *
     * @return int|string
     */
    public function getLayoutId();

    /**
     * Returns zone identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns zone blocks.
     *
     * @return string
     */
    public function getBlocks();
}
