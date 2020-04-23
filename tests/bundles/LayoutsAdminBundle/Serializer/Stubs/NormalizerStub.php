<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs;

use Generator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function is_object;

final class NormalizerStub implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = []): string
    {
        return 'data';
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return is_object($data) && !$data instanceof Generator;
    }
}
