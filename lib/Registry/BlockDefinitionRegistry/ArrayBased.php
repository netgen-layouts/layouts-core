<?php

namespace Netgen\BlockManager\Registry\BlockDefinitionRegistry;

use Netgen\BlockManager\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface;
use InvalidArgumentException;

class ArrayBased implements BlockDefinitionRegistry
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface[]
     */
    protected $blockDefinitions = array();

    /**
     * Adds a block definition to registry.
     *
     * @param \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface $blockDefinition
     */
    public function addBlockDefinition(BlockDefinitionInterface $blockDefinition)
    {
        $this->blockDefinitions[$blockDefinition->getIdentifier()] = $blockDefinition;
    }

    /**
     * Returns a block definition with provided identifier.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface
     */
    public function getBlockDefinition($identifier)
    {
        if (isset($this->blockDefinitions[$identifier])) {
            return $this->blockDefinitions[$identifier];
        }

        throw new InvalidArgumentException('Block definition with "' . $identifier . '" identifier does not exist.');
    }

    /**
     * Returns all block definitions.
     *
     * @return \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface[]
     */
    public function getBlockDefinitions()
    {
        return $this->blockDefinitions;
    }
}
