<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\Serializer\SerializerAwareTrait;
use Netgen\BlockManager\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

final class ValueNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @param \Netgen\BlockManager\Serializer\Values\Value $object
     * @param string $format
     * @param array $context
     *
     * @return mixed
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $this->serializer->normalize($object->getValue(), $format, $context);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Value;
    }
}
