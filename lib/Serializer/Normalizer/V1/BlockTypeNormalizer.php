<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockTypeNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Block\BlockType\BlockType $blockType */
        $blockType = $object->getValue();
        $blockDefinition = $blockType->getDefinition();

        $isContainer = $blockDefinition instanceof ContainerDefinitionInterface;

        return array(
            'identifier' => $blockType->getIdentifier(),
            'enabled' => $blockType->isEnabled(),
            'name' => $blockType->getName(),
            'icon' => $blockType->getIcon(),
            'definition_identifier' => $blockDefinition->getIdentifier(),
            'is_container' => $isContainer,
            'is_dynamic_container' => $isContainer && $blockDefinition->isDynamicContainer(),
            'defaults' => $blockType->getDefaults(),
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof BlockType && $data->getVersion() === Version::API_V1;
    }
}
