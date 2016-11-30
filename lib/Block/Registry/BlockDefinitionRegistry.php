<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class BlockDefinitionRegistry implements BlockDefinitionRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface[]
     */
    protected $blockDefinitions = array();

    /**
     * Adds a block definition to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     */
    public function addBlockDefinition($identifier, BlockDefinitionInterface $blockDefinition)
    {
        $this->blockDefinitions[$identifier] = $blockDefinition;
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

    /**
     * Returns a block definition with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If block definition does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getBlockDefinition($identifier)
    {
        if (!$this->hasBlockDefinition($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Block definition with "%s" identifier does not exist.',
                    $identifier
                )
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
}
