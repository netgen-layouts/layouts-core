<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller as BaseController;
use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\API\Values\Value;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Controller extends BaseController
{
    /**
     * @const string
     */
    const API_VERSION = 1;

    /**
     * Builds the array which will be serialized from provided value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return array
     */
    protected function buildData(Value $value)
    {
        $data = $this->normalizeValueObject($value);

        $view = $this->buildViewObject(
            $value,
            ViewInterface::CONTEXT_API,
            array('api_version' => self::API_VERSION)
        );

        $data['html'] = $this->renderViewObject($view);

        return $data;
    }

    /**
     * Builds the array which will be serialized from provided value object and form.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return array
     */
    protected function buildDataForForm(Value $value, FormInterface $form)
    {
        $view = $this->buildViewObject(
            $value,
            ViewInterface::CONTEXT_API_EDIT,
            array(
                'form' => $form->createView(),
                'api_version' => self::API_VERSION,
            )
        );

        $data = array('form' => $this->renderViewObject($view));

        return $data;
    }

    /**
     * Builds the response from provided data.
     *
     * @param array $data
     * @param int $statusCode
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function buildResponse(array $data, $statusCode = Response::HTTP_OK)
    {
        $response = new JsonResponse(null, $statusCode);
        $response->setContent(
            $this->serializeData($data)
        );

        return $response;
    }

    /**
     * Normalized the provided value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return array
     */
    protected function normalizeValueObject(Value $value)
    {
        $serializer = $this->get('serializer');

        return $serializer->normalize(
            new SerializableValue(
                $value,
                self::API_VERSION
            )
        );
    }

    /**
     * Serializes the provided value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return string
     */
    protected function serializeValueObject(Value $value)
    {
        $normalizedValueObject = $this->normalizeValueObject($value);

        return $this->serializeData($normalizedValueObject);
    }

    /**
     * Serializes the provided data.
     *
     * @param array $data
     *
     * @return string
     */
    public function serializeData(array $data)
    {
        $serializer = $this->get('serializer');

        return $serializer->encode($data, 'json');
    }
}
