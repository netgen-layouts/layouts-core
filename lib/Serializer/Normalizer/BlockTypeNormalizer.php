<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer;

use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\ContainerDefinitionInterface;
use Netgen\Layouts\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockTypeNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\Block\BlockType\BlockType $blockType */
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

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof BlockType;
    }
}
