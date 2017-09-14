<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BlockTypeGroupNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup $blockTypeGroup */
        $blockTypeGroup = $object->getValue();

        return array(
            'identifier' => $blockTypeGroup->getIdentifier(),
            'enabled' => $blockTypeGroup->isEnabled(),
            'name' => $blockTypeGroup->getName(),
            'block_types' => array_map(
                function (BlockType $blockType) {
                    return $blockType->getIdentifier();
                },
                $blockTypeGroup->getBlockTypes(true)
            ),
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof BlockTypeGroup && $data->getVersion() === Version::API_V1;
    }
}
