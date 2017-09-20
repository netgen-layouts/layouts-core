<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LayoutTypeNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Layout\Type\LayoutType $layoutType */
        $layoutType = $object->getValue();

        return array(
            'identifier' => $layoutType->getIdentifier(),
            'name' => $layoutType->getName(),
            'icon' => $layoutType->getIcon(),
            'zones' => $this->getZones($layoutType),
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof LayoutType && $data->getVersion() === Version::API_V1;
    }

    /**
     * Returns the array with layout type zones.
     *
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     *
     * @return array
     */
    private function getZones(LayoutType $layoutType)
    {
        $zones = array();

        foreach ($layoutType->getZones() as $zone) {
            $zones[] = array(
                'identifier' => $zone->getIdentifier(),
                'name' => $zone->getName(),
                'allowed_block_definitions' => !empty($zone->getAllowedBlockDefinitions()) ?
                    $zone->getAllowedBlockDefinitions() :
                    true,
            );
        }

        return $zones;
    }
}
