<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Netgen\BlockManager\Serializer\Version;

class BlockNormalizer implements NormalizerInterface
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
        /** @var \Netgen\BlockManager\API\Values\Page\Block $block */
        $block = $object->getValue();

        return array(
            'id' => $block->getId(),
            'definition_identifier' => $block->getDefinitionIdentifier(),
            'name' => $block->getName(),
            'zone_identifier' => $block->getZoneIdentifier(),
            'position' => $block->getPosition(),
            'layout_id' => $block->getLayoutId(),
            'parameters' => $block->getParameters(),
            'view_type' => $block->getViewType(),
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

        return $data->getValue() instanceof Block && $data->getVersion() === Version::API_V1;
    }
}
