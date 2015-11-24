<?php

namespace Netgen\BlockManager\BlockDefinition\Registry;

use Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface;
use InvalidArgumentException;

class BlockDefinitionRegistry implements BlockDefinitionRegistryInterface
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
        if (!$this->hasBlockDefinition($identifier)) {
            throw new InvalidArgumentException(
                'Block definition with "' . $identifier . '" identifier does not exist.'
            );
        }

        return $this->blockDefinitions[$identifier];
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

    /**
     * Returns if registry has a block definition.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockDefinition($identifier)
    {
        return isset($this->blockDefinitions[$identifier]);
    }
}
