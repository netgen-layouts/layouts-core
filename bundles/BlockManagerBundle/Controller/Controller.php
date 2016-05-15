<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Returns the specified block definition from the registry.
     *
     * @param string $definitionIdentifier
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected function getBlockDefinition($definitionIdentifier)
    {
        $blockDefinitionRegistry = $this->get('netgen_block_manager.block.registry.block_definition');

        return $blockDefinitionRegistry->getBlockDefinition($definitionIdentifier);
    }

    /**
     * Returns the specified query type from the registry.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    protected function getQueryType($identifier)
    {
        $queryTypeRegistry = $this->get('netgen_block_manager.collection.registry.query_type');

        return $queryTypeRegistry->getQueryType($identifier);
    }

    /**
     * Returns the specified layout type from the registry.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\LayoutType\LayoutType
     */
    protected function getLayoutType($identifier)
    {
        $layoutTypeRegistry = $this->get('netgen_block_manager.configuration.registry.layout_type');

        return $layoutTypeRegistry->getLayoutType($identifier);
    }

    /**
     * Returns the specified block type from the registry.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    protected function getBlockType($identifier)
    {
        $blockTypeRegistry = $this->get('netgen_block_manager.configuration.registry.block_type');

        return $blockTypeRegistry->getBlockType($identifier);
    }
}
