<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Generator;
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
     * @param mixed $object
     * @param string|null $format
     *
     * @return array<string, mixed>
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\Layout\Type\LayoutTypeInterface $layoutType */
        $layoutType = $object->getValue();

        return [
            'identifier' => $layoutType->getIdentifier(),
            'name' => $layoutType->getName(),
            'icon' => $layoutType->getIcon(),
            'zones' => $this->normalizer->normalize($this->getZones($layoutType), $format, $context),
        ];
    }

    /**
     * @param mixed $data
     * @param string|null $format
     */
    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof LayoutTypeInterface;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }

    /**
     * Returns the array with layout type zones.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function getZones(LayoutTypeInterface $layoutType): Generator
    {
        foreach ($layoutType->getZones() as $zone) {
            $allowedBlockDefinitions = $zone->getAllowedBlockDefinitions();

            yield [
                'identifier' => $zone->getIdentifier(),
                'name' => $zone->getName(),
                'allowed_block_definitions' => count($allowedBlockDefinitions) > 0 ?
                    $allowedBlockDefinitions :
                    true,
            ];
        }
    }
}
