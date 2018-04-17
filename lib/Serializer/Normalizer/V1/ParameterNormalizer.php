<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ParameterNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\Parameters\Parameter $parameter */
        $parameter = $object->getValue();
        $parameterDefinition = $parameter->getParameterDefinition();

        return $parameterDefinition->getType()->toHash($parameterDefinition, $parameter->getValue());
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Parameter && $data->getVersion() === Version::API_V1;
    }
}
