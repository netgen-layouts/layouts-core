<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinitionInterface;

interface BlockDefinitionRegistryInterface
{
    /**
     * Adds a block definition to registry.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     */
    public function addBlockDefinition(BlockDefinitionInterface $blockDefinition);

    /**
     * Returns a block definition with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \InvalidArgumentException If block definition does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getBlockDefinition($identifier);

    /**
     * Returns all block definitions.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface[]
     */
    public function getBlockDefinitions();

    /**
     * Returns if registry has a block definition.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockDefinition($identifier);
}
