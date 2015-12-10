<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\View\ViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Builds the view from the object.
     *
     * @param mixed $object
     * @param string $context
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    protected function buildViewObject($object, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array())
    {
        $viewBuilder = $this->get('netgen_block_manager.view.builder');

        return $viewBuilder->buildView($object, $context, $parameters);
    }

    /**
     * Returns the specified block definition from the registry.
     *
     * @param string $definitionIdentifier
     *
     * @return \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface
     */
    protected function getBlockDefinition($definitionIdentifier)
    {
        $blockDefinitionRegistry = $this->get('netgen_block_manager.block_definition.registry');

        return $blockDefinitionRegistry->getBlockDefinition($definitionIdentifier);
    }
}
