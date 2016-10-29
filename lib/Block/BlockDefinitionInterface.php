<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Page\Block;

interface BlockDefinitionInterface
{
    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters();

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Block\DynamicParameters
     */
    public function getDynamicParameters(Block $block);

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection();

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function getHandler();

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig();
}
