<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Page\Block;

interface BlockDefinitionHandlerInterface
{
    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters();

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array());
}
