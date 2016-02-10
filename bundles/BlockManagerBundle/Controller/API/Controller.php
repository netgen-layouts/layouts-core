<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\Bundle\BlockManagerBundle\Controller\Controller as BaseController;
use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\API\Values\Value;

abstract class Controller extends BaseController
{
    /**
     * @const string
     */
    const API_VERSION = 1;

    public function normalizeValueObject(Value $value)
    {
        $serializer = $this->get('serializer');

        $normalizedValue = $serializer->normalize(
            new SerializableValue(
                $value,
                self::API_VERSION
            )
        );

        $view = $this->buildViewObject(
            $value,
            ViewInterface::CONTEXT_API,
            array('api_version' => self::API_VERSION)
        );

        $normalizedValue['html'] = $this->renderViewObject($view);

        return $normalizedValue;
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
