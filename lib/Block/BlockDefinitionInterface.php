<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

/**
 * Block definition represents the model of the block, built from configuration.
 * This model specifies which parameters, view types and item view types
 * the block can have.
 */
interface BlockDefinitionInterface extends ParameterCollectionInterface
{
    /**
     * Returns the block definition identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Block\DynamicParameters
     */
    public function getDynamicParameters(Block $block);

    /**
     * Returns if the provided block is dependent on a context, i.e. current request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function isContextual(Block $block);

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig();

    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions();

    /**
     * Returns if the block definition has a plugin with provided FQCN.
     *
     * @param string $className
     *
     * @return bool
     */
    public function hasPlugin($className);
}
