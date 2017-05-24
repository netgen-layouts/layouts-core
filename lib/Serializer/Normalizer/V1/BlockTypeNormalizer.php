<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
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
        /** @var \Netgen\BlockManager\Block\BlockType\BlockType $blockType */
        $blockType = $object->getValue();
        $blockDefinition = $blockType->getDefinition();

        $isContainer = $blockDefinition instanceof ContainerDefinitionInterface;

        return array(
            'identifier' => $blockType->getIdentifier(),
            'name' => $blockType->getName(),
            'definition_identifier' => $blockDefinition->getIdentifier(),
            'is_container' => $isContainer,
            'is_dynamic_container' => $isContainer && $blockDefinition->isDynamicContainer(),
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
