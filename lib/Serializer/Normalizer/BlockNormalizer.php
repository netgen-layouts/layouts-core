<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Netgen\BlockManager\API\Values\Page\Block;

class BlockNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $blockDefinitionIdentifier = $object->getDefinitionIdentifier();

        return array(
            'id' => $object->getId(),
            'definition_identifier' => $blockDefinitionIdentifier,
            'name' => $object->getName(),
            'zone_id' => $object->getZoneId(),
            'parameters' => $object->getParameters(),
            'view_type' => $object->getViewType(),
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
        return $data instanceof Block;
    }
}
