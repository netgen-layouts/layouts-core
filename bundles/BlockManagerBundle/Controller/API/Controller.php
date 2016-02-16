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

    public function handleValueObject(Value $value)
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

    public function handleValueObjectForm(Value $value, FormInterface $form)
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

    public function buildResponse(array $data, $statusCode = Response::HTTP_OK)
    {
        $response = new JsonResponse(null, $statusCode);
        $response->setContent(
            $this->serializeData($data)
        );

        return $response;
    }

    public function normalizeValueObject(Value $value)
    {
        $serializer = $this->get('serializer');

        return $serializer->normalize(
            new SerializableValue(
                $value,
                self::API_VERSION
            )
        );
    }

    public function serializeValueObject(Value $value)
    {
        $normalizedValueObject = $this->normalizeValueObject($value);

        return $this->serializeData($normalizedValueObject);
    }

    public function serializeData(array $data)
    {
        $serializer = $this->get('serializer');

        return $serializer->encode($data, 'json');
    }
}
