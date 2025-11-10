<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Slot;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionSlotNormalizer implements NormalizerInterface
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\API\Values\Collection\Slot $slot */
        $slot = $object->getValue();

        return [
            'id' => $slot->getId()->toString(),
            'collection_id' => $slot->getCollectionId()->toString(),
            'position' => $slot->getPosition(),
            'view_type' => $slot->getViewType(),
            'empty' => $slot->isEmpty(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Slot;
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
