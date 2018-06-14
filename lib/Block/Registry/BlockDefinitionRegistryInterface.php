<?php

declare(strict_types=1);

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
    public function addBlockDefinition(string $identifier, BlockDefinitionInterface $blockDefinition): void;

    /**
     * Returns if registry has a block definition.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockDefinition(string $identifier): bool;

    /**
     * Returns a block definition with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If block definition does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getBlockDefinition(string $identifier): BlockDefinitionInterface;

    /**
     * Returns all block definitions.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface[]
     */
    public function getBlockDefinitions(): array;
}
