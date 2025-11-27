<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_map;

final class BlockTypeGroupNormalizer implements NormalizerInterface
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\Block\BlockType\BlockTypeGroup $blockTypeGroup */
        $blockTypeGroup = $data->value;

        return [
            'identifier' => $blockTypeGroup->identifier,
            'enabled' => $blockTypeGroup->isEnabled,
            'name' => $blockTypeGroup->name,
            'block_types' => array_map(
                static fn (BlockType $blockType): string => $blockType->identifier,
                $blockTypeGroup->enabledBlockTypes,
            ),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->value instanceof BlockTypeGroup;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }
}
