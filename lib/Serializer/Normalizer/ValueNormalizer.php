<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ValueNormalizer extends SerializerAwareNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\Value $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $this->serializer->normalize($object->getValue(), $format, $context);
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
        return $data instanceof Value;
    }
}
