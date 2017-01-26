<?php

namespace Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BlockTypeNormalizer implements NormalizerInterface
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
        /** @var \Netgen\BlockManager\Configuration\BlockType\BlockType $blockType */
        $blockType = $object->getValue();
        $blockDefinition = $blockType->getDefinition();

        return array(
            'identifier' => $blockType->getIdentifier(),
            'name' => $blockType->getName(),
            'definition_identifier' => $blockDefinition->getIdentifier(),
            'is_container' => $blockDefinition->isContainer(),
            'is_dynamic_container' => $blockDefinition->isDynamicContainer(),
            'defaults' => $blockType->getDefaults(),
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

        return $data->getValue() instanceof BlockType && $data->getVersion() === Version::API_V1;
    }
}
