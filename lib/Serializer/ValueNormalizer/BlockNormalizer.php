<?php

namespace Netgen\BlockManager\Serializer\ValueNormalizer;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BlockNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\Value $object
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
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Block && $data->getVersion() === 1;
    }
}
