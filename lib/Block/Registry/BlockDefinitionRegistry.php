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
use function count;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Block\BlockDefinitionInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Block\BlockDefinitionInterface>
 */
final class BlockDefinitionRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Block\BlockDefinitionInterface>
     */
    private array $blockDefinitions;

    /**
     * @param array<string, \Netgen\Layouts\Block\BlockDefinitionInterface> $blockDefinitions
     */
    public function __construct(array $blockDefinitions)
    {
        $this->blockDefinitions = array_filter(
            $blockDefinitions,
            static fn (BlockDefinitionInterface $blockDefinition): bool => true,
        );
    }

    /**
     * Returns if registry has a block definition.
     */
    public function hasBlockDefinition(string $identifier): bool
    {
        return isset($this->blockDefinitions[$identifier]);
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

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->hasBlockDefinition($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): BlockDefinitionInterface
    {
        return $this->getBlockDefinition($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
