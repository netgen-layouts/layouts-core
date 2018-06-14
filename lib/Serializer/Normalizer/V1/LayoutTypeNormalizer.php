<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class LayoutTypeNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\Layout\Type\LayoutTypeInterface $layoutType */
        $layoutType = $object->getValue();

        return [
            'identifier' => $layoutType->getIdentifier(),
            'name' => $layoutType->getName(),
            'icon' => $layoutType->getIcon(),
            'zones' => $this->getZones($layoutType),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof LayoutTypeInterface && $data->getVersion() === Version::API_V1;
    }

    /**
     * Returns the array with layout type zones.
     */
    private function getZones(LayoutTypeInterface $layoutType): array
    {
        $zones = [];

        foreach ($layoutType->getZones() as $zone) {
            $zones[] = [
                'identifier' => $zone->getIdentifier(),
                'name' => $zone->getName(),
                'allowed_block_definitions' => !empty($zone->getAllowedBlockDefinitions()) ?
                    $zone->getAllowedBlockDefinitions() :
                    true,
            ];
        }

        return $zones;
    }
}
