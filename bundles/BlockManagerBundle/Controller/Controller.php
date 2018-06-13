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
        $this->checkPermissions();
    }

    /**
     * Performs access checks on the controller.
     */
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Builds the view from provided value.
     *
     * @param mixed $value
     * @param string $context
     * @param array $parameters
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    protected function buildView(
        $value,
        $context = ViewInterface::CONTEXT_DEFAULT,
        array $parameters = [],
        Response $response = null
    ) {
        /** @var \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder */
        $viewBuilder = $this->get('netgen_block_manager.view.view_builder');
        $view = $viewBuilder->buildView($value, $context, $parameters);

        $view->setResponse($response instanceof Response ? $response : new Response());

        return $view;
    }
}
