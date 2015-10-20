<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\REST;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;

abstract class Controller extends BaseController
{
    /**
     * @const string
     */
    const API_VERSION = 1;

    /**
     * Serializes the object.
     *
     * @param mixed $object
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function serializeObject($object)
    {
        $serializationContext = new SerializationContext();
        $serializationContext->setVersion(self::API_VERSION);
        $serializationContext->setSerializeNull(true);

        $serializedObject = $this->get('jms_serializer')->serialize(
            $object,
            'json',
            $serializationContext
        );

        $response = new JsonResponse();
        $response->setContent($serializedObject);

        return $response;
    }

    /**
     * Builds the view from the object.
     *
     * @param mixed $object
     * @param array $parameters
     * @param string $context
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    protected function buildViewObject($object, $parameters = array(), $context = 'view')
    {
        $viewBuilderRegistry = $this->get('netgen_block_manager.registry.view_builder');
        $viewBuilder = $viewBuilderRegistry->getViewBuilder($object);

        return $viewBuilder->buildView($object, $parameters, $context);
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
}
