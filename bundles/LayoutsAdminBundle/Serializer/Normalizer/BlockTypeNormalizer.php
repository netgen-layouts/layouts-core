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
     * @param mixed $object
     * @param string|null $format
     *
     * @return array<string, mixed>
     */
    public function normalize($object, $format = null, array $context = []): array
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

    /**
     * @param mixed $data
     * @param string|null $format
     */
    public function supportsNormalization($data, $format = null): bool
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
