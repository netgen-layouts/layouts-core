<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\Serializer\Normalizer;
use Netgen\BlockManager\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ValueNormalizer extends Normalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\Serializer\Values\Value $object */

        return $this->normalizer->normalize($object->getValue(), $format, $context);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Value;
    }
}
