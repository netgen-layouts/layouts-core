<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function is_iterable;
use function is_object;

final class NormalizerStub implements NormalizerInterface
{
    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        return 'data';
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return is_object($data) && !is_iterable($data);
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
