<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

interface BlockDefinitionInterface extends ParameterCollectionInterface
{
    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return array
     */
    public function getDynamicParameters(Block $block);

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig();
}
