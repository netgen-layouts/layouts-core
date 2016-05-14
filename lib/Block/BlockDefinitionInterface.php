<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition as Configuration;

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
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters();

    /**
     * Returns the array specifying block parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints();

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
     * Sets the block definition configuration
     *
     * @param \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition $configuration
     */
    public function setConfiguration(Configuration $configuration);

    /**
     * Returns the block definition configuration
     *
     * @return \Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition $configuration
     */
    public function getConfiguration();
}
