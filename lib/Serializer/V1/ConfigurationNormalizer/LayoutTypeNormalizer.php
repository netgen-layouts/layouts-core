<?php

namespace Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LayoutTypeNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\VersionedValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType */
        $layoutType = $object->getValue();

        return array(
            'identifier' => $layoutType->getIdentifier(),
            'name' => $layoutType->getName(),
            'zones' => $this->getZones($layoutType),
        );
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
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof LayoutType && $data->getVersion() === Version::API_V1;
    }

    /**
     * Returns the array with layout type zones.
     *
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     *
     * @return array
     */
    protected function getZones(LayoutType $layoutType)
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
