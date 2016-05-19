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
     * Returns layout ID to which this block belongs.
     *
     * @return int|string
     */
    public function getLayoutId();

    /**
     * Returns zone identifier to which this block belongs.
     *
     * @return string
     */
    public function getZoneIdentifier();

    /**
     * Returns the position of this block in the zone.
     *
     * @return int
     */
    public function getPosition();

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
     * Returns specified block parameter.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getParameter($parameter);

    /**
     * Returns if block has a specified parameter.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter);

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

    /**
     * Returns the status of the block.
     *
     * @return int
     */
    public function getStatus();
}
