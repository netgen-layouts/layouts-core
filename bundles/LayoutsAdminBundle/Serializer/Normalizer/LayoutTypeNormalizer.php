<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function count;

final class LayoutTypeNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\Layout\Type\LayoutTypeInterface $layoutType */
        $layoutType = $data->value;

        return [
            'identifier' => $layoutType->identifier,
            'name' => $layoutType->name,
            'icon' => $layoutType->icon,
            'zones' => $this->normalizer->normalize($this->getZones($layoutType), $format, $context),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->value instanceof LayoutTypeInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }

    /**
     * Returns the array with layout type zones.
     *
     * @return iterable<array<string, mixed>>
     */
    private function getZones(LayoutTypeInterface $layoutType): iterable
    {
        foreach ($layoutType->zones as $zone) {
            yield [
                'identifier' => $zone->identifier,
                'name' => $zone->name,
                'allowed_block_definitions' => count($zone->allowedBlockDefinitions) > 0 ?
                    $zone->allowedBlockDefinitions :
                    true,
            ];
        }
    }
}
