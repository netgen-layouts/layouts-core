<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\View\ViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller extends BaseController
{
    /**
     * Initializes the controller by setting the container and performing basic access checks.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function initialize(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Builds the view from provided value object.
     *
     * @param mixed $value
     * @param array $parameters
     * @param string $context
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    protected function buildView(
        $value,
        array $parameters = array(),
        $context = ViewInterface::CONTEXT_DEFAULT,
        Response $response = null
    ) {
        $viewBuilder = $this->get('netgen_block_manager.view.builder');
        $view = $viewBuilder->buildView($value, $parameters, $context);

        if ($response instanceof Response) {
            $view->setResponse($response);
        }

        return $view;
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

    /**
     * Returns if the specified value type exists in the registry.
     *
     * @param string $type
     *
     * @return bool
     */
    protected function hasValueType($type)
    {
        $valueLoaderRegistry = $this->get('netgen_block_manager.item.registry.value_loader');

        return $valueLoaderRegistry->hasValueLoader($type);
    }
}
