<?php

namespace Netgen\BlockManager\API\Values\Page;

interface Block
{
    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns zone ID to which this block belongs.
     *
     * @return int|string
     */
    public function getZoneId();

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getDefinitionIdentifier();

    /**
     * Returns block parameters.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType();

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName();
}
