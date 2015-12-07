<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

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
    protected function buildViewObject($object, $context = 'view', array $parameters = array())
    {
        $viewBuilder = $this->get('netgen_block_manager.view.builder');

        return $viewBuilder->buildView($object, $context, $parameters);
    }

    /**
     * Renders the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderViewObject($view)
    {
        $viewRenderer = $this->get('netgen_block_manager.view.renderer');
        $renderedView = $viewRenderer->renderView($view);

        $response = new Response();
        $response->setContent($renderedView);

        return $response;
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
