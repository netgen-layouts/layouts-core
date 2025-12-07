<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

use function array_filter;
use function array_key_exists;
use function count;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Block\BlockDefinitionInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Block\BlockDefinitionInterface>
 */
final class BlockDefinitionRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @param array<string, \Netgen\Layouts\Block\BlockDefinitionInterface> $blockDefinitions
     */
    public function __construct(
        private array $blockDefinitions,
    ) {
        $this->blockDefinitions = array_filter(
            $this->blockDefinitions,
            static fn (BlockDefinitionInterface $blockDefinition): bool => true,
        );
    }

    /**
     * Returns if registry has a block definition.
     */
    public function hasBlockDefinition(string $identifier): bool
    {
        return array_key_exists($identifier, $this->blockDefinitions);
    }

    /**
     * Returns a block definition with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockDefinitionException If block definition does not exist
     */
    public function getBlockDefinition(string $identifier): BlockDefinitionInterface
    {
        if (!$this->hasBlockDefinition($identifier)) {
            throw BlockDefinitionException::noBlockDefinition($identifier);
        }

        return $this->blockDefinitions[$identifier];
    }

    /**
     * Returns all block definitions.
     *
     * @return array<string, \Netgen\Layouts\Block\BlockDefinitionInterface>
     */
    public function getBlockDefinitions(): array
    {
        return $this->blockDefinitions;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->blockDefinitions);
    }

    public function count(): int
    {
        return count($this->blockDefinitions);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasBlockDefinition($offset);
    }

    public function offsetGet(mixed $offset): BlockDefinitionInterface
    {
        return $this->getBlockDefinition($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new RuntimeException('Method call not supported.');
    }
}
