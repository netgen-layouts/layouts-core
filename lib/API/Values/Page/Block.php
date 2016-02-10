<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;

abstract class Block extends Value
{
    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    abstract public function getId();

    /**
     * Returns layout ID to which this block belongs.
     *
     * @return int|string
     */
    abstract public function getLayoutId();

    /**
     * Returns zone identifier to which this block belongs.
     *
     * @return string
     */
    abstract public function getZoneIdentifier();

    /**
     * Returns the position of this block in the zone.
     *
     * @return int
     */
    abstract public function getPosition();

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    abstract public function getDefinitionIdentifier();

    /**
     * Returns block parameters.
     *
     * @return array
     */
    abstract public function getParameters();

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    abstract public function getViewType();

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Returns the status of the block.
     *
     * @return string
     */
    abstract public function getStatus();
}
