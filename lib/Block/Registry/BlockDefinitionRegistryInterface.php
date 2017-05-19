<?php

namespace Netgen\BlockManager\Block\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

interface BlockDefinitionRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a block definition to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     */
    public function addBlockDefinition($identifier, BlockDefinitionInterface $blockDefinition);

    /**
     * Returns if registry has a block definition.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockDefinition($identifier);

    /**
     * Returns a block definition with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If block definition does not exist
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
}
