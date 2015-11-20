<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

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
            $object,
            'json',
            array('version' => $apiVersion)
        );

        $response = new JsonResponse();
        $response->setContent($serializedObject);

        return $response;
    }
}
