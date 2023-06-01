<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs;

use Generator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function is_object;

final class NormalizerStub implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param string|null $format
     */
    public function normalize($object, $format = null, array $context = []): string
    {
        return 'data';
    }

    /**
     * @param mixed $data
     * @param string|null $format
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return is_object($data) && !$data instanceof Generator;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => false,
        ];
    }
}
