<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Block\ContainerDefinitionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockTypeNormalizer implements NormalizerInterface
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\Block\BlockType\BlockType $blockType */
        $blockType = $data->getValue();

        $normalizedData = [
            'identifier' => $blockType->identifier,
            'enabled' => $blockType->isEnabled,
            'name' => $blockType->name,
            'icon' => $blockType->icon,
            'definition_identifier' => $blockType->definition->identifier,
            'is_container' => false,
            'defaults' => $blockType->defaults,
        ];

        if ($blockType->definition instanceof ContainerDefinitionInterface) {
            $normalizedData['is_container'] = true;
        }

        return $normalizedData;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof BlockType;
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
