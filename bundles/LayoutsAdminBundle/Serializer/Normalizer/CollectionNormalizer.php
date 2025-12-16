<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Collection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionNormalizer implements NormalizerInterface
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\API\Values\Collection\Collection $collection */
        $collection = $data->value;

        return [
            'id' => $collection->id->toString(),
            'type' => $collection->collectionType->value,
            'is_translatable' => $collection->isTranslatable,
            'main_locale' => $collection->mainLocale,
            'always_available' => $collection->isAlwaysAvailable,
            'available_locales' => $collection->availableLocales,
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->value instanceof Collection;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }
}
