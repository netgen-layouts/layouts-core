<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition as Config;

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
     * The keys are parameter identifiers.
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

    /**
     * Sets the block definition configuration.
     *
     * @param \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition $config
     */
    public function setConfig(Config $config);

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition
     */
    public function getConfig();
}
