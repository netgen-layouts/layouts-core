<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Page\Block;
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
     * Returns placeholder definitions.
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface[]
     */
    public function getPlaceholders();

    /**
     * Returns a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException if the placeholder does not exist
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    public function getPlaceholder($placeholderIdentifier);

    /**
     * Returns if block definition has a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @return bool
     */
    public function hasPlaceholder($placeholderIdentifier);

    /**
     * Returns dynamic placeholder definition.
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    public function getDynamicPlaceholder();

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
     * Returns if this block definition is a container.
     *
     * @return bool
     */
    public function isContainer();

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer();

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection();

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig();
}
