<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use InvalidArgumentException;

class BlockDefinitionRegistry implements BlockDefinitionRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface[]
     */
    protected $blockDefinitions = array();

    /**
     * Adds a block definition to registry.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
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
     * @throws \InvalidArgumentException If block definition does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
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
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface[]
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
