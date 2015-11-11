<?php

namespace Netgen\BlockManager\BlockDefinition\Registry;

use Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface;

interface BlockDefinitionRegistryInterface
{
    /**
     * Adds a block definition to registry.
     *
     * @param \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface $blockDefinition
     */
    public function addBlockDefinition(BlockDefinitionInterface $blockDefinition);

    /**
     * Returns a block definition with provided identifier.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface
     */
    public function getBlockDefinition($identifier);

    /**
     * Returns all block definitions.
     *
     * @return \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface[]
     */
    public function getBlockDefinitions();
}
