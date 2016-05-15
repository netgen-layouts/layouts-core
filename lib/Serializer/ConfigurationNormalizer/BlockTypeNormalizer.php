<?php

namespace Netgen\BlockManager\Serializer\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
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

        return array(
            'identifier' => $blockType->getIdentifier(),
            'name' => $blockType->getName(),
            'definition_identifier' => $blockType->getDefinitionIdentifier(),
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

        return $data->getValue() instanceof BlockType && $data->getVersion() === 1;
    }
}
