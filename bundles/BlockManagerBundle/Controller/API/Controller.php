<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\View\SerializableView;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @param int $apiVersion
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function serializeObject($object, $apiVersion)
    {
        $serializedObject = $this->get('serializer')->serialize(
            new SerializableView($object, $apiVersion),
            'json'
        );

        $response = new JsonResponse();
        $response->setContent($serializedObject);

        return $response;
    }
}
