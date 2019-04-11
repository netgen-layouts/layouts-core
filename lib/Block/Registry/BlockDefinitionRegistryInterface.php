<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Block\BlockDefinitionInterface;

interface BlockDefinitionRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a block definition.
     */
    public function hasBlockDefinition(string $identifier): bool;

    /**
     * Returns a block definition with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockDefinitionException If block definition does not exist
     */
    public function getBlockDefinition(string $identifier): BlockDefinitionInterface;

    /**
     * Returns all block definitions.
     *
     * @return \Netgen\Layouts\Block\BlockDefinitionInterface[]
     */
    public function getBlockDefinitions(): array;
}
