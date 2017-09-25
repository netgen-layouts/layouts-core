<?php

namespace Netgen\BlockManager\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Exception\RuntimeException;

final class BlockDefinitionRegistry implements BlockDefinitionRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface[]
     */
    private $blockDefinitions = array();

    public function addBlockDefinition($identifier, BlockDefinitionInterface $blockDefinition)
    {
        $this->blockDefinitions[$identifier] = $blockDefinition;
    }

    public function hasBlockDefinition($identifier)
    {
        return isset($this->blockDefinitions[$identifier]);
    }

    public function getBlockDefinition($identifier)
    {
        if (!$this->hasBlockDefinition($identifier)) {
            throw BlockDefinitionException::noBlockDefinition($identifier);
        }

        return $this->blockDefinitions[$identifier];
    }

    public function getBlockDefinitions()
    {
        return $this->blockDefinitions;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->blockDefinitions);
    }

    public function count()
    {
        return count($this->blockDefinitions);
    }

    public function offsetExists($offset)
    {
        return $this->hasBlockDefinition($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getBlockDefinition($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
