<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ParameterValueNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\VersionedValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Parameters\ParameterValue $parameterValue */
        $parameterValue = $object->getValue();
        $parameter = $parameterValue->getParameter();

        return $parameter->getType()->toHash($parameter, $parameterValue->getValue());
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof ParameterValue && $data->getVersion() === Version::API_V1;
    }
}
