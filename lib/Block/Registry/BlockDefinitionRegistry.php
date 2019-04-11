<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use ArrayIterator;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

final class BlockDefinitionRegistry implements BlockDefinitionRegistryInterface
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinitionInterface[]
     */
    private $blockDefinitions;

    /**
     * @param \Netgen\Layouts\Block\BlockDefinitionInterface[] $blockDefinitions
     */
    public function __construct(array $blockDefinitions)
    {
        $this->blockDefinitions = array_filter(
            $blockDefinitions,
            static function (BlockDefinitionInterface $blockDefinition): bool {
                return true;
            }
        );
    }

    public function hasBlockDefinition(string $identifier): bool
    {
        return isset($this->blockDefinitions[$identifier]);
    }

    public function getBlockDefinition(string $identifier): BlockDefinitionInterface
    {
        if (!$this->hasBlockDefinition($identifier)) {
            throw BlockDefinitionException::noBlockDefinition($identifier);
        }

        return $this->blockDefinitions[$identifier];
    }

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
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasBlockDefinition($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
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
