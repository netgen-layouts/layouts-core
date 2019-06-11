<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ParameterNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\Parameters\Parameter $parameter */
        $parameter = $object->getValue();
        $parameterDefinition = $parameter->getParameterDefinition();

        return $parameterDefinition->getType()->toHash($parameterDefinition, $parameter->getValue());
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Parameter;
    }
}
