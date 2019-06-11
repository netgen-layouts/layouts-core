<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\API\Values\Collection\Collection $collection */
        $collection = $object->getValue();

        return [
            'id' => $collection->getId()->toString(),
            'type' => $collection->hasQuery() ? Collection::TYPE_DYNAMIC : Collection::TYPE_MANUAL,
            'is_translatable' => $collection->isTranslatable(),
            'main_locale' => $collection->getMainLocale(),
            'always_available' => $collection->isAlwaysAvailable(),
            'available_locales' => $collection->getAvailableLocales(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Collection;
    }
}
