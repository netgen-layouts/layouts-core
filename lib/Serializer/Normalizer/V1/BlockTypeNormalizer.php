<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockTypeNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\Block\BlockType\BlockType $blockType */
        $blockType = $object->getValue();
        $blockDefinition = $blockType->getDefinition();

        $data = [
            'identifier' => $blockType->getIdentifier(),
            'enabled' => $blockType->isEnabled(),
            'name' => $blockType->getName(),
            'icon' => $blockType->getIcon(),
            'definition_identifier' => $blockDefinition->getIdentifier(),
            'is_container' => false,
            'defaults' => $blockType->getDefaults(),
        ];

        if ($blockDefinition instanceof ContainerDefinitionInterface) {
            $data['is_container'] = true;
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof BlockType && $data->getVersion() === Version::API_V1;
    }
}
