<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;

interface Block extends Value
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
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getBlockDefinition();

    /**
     * Returns block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterVO[]
     */
    public function getParameters();

    /**
     * Returns specified block parameter.
     *
     * @param string $parameter
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterVO
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
     * Returns item view type which will be used to render block items.
     *
     * @return string
     */
    public function getItemViewType();

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
