<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ParameterValueNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Parameters\ParameterValue $parameterValue */
        $parameterValue = $object->getValue();
        $parameter = $parameterValue->getParameter();

        return $parameter->getType()->toHash($parameter, $parameterValue->getValue());
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof ParameterValue && $data->getVersion() === Version::API_V1;
    }
}
