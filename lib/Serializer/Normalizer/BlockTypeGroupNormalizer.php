<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer;

use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use Netgen\Layouts\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockTypeGroupNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\Block\BlockType\BlockTypeGroup $blockTypeGroup */
        $blockTypeGroup = $object->getValue();

        return [
            'identifier' => $blockTypeGroup->getIdentifier(),
            'enabled' => $blockTypeGroup->isEnabled(),
            'name' => $blockTypeGroup->getName(),
            'block_types' => array_map(
                static function (BlockType $blockType): string {
                    return $blockType->getIdentifier();
                },
                $blockTypeGroup->getBlockTypes(true)
            ),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof BlockTypeGroup;
    }
}
