<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Generator;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Serializer\Normalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionQueryNormalizer extends Normalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\API\Values\Collection\Query $query */
        $query = $object->getValue();

        $parameters = $this->buildVersionedValues($query->getParameters(), $object->getVersion());

        return [
            'id' => $query->getId(),
            'collection_id' => $query->getCollectionId(),
            'type' => $query->getQueryType()->getType(),
            'locale' => $query->getLocale(),
            'is_translatable' => $query->isTranslatable(),
            'always_available' => $query->isAlwaysAvailable(),
            'parameters' => $this->normalizer->normalize($parameters, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Query && $data->getVersion() === Version::API_V1;
    }

    /**
     * Builds the list of VersionedValue objects for provided list of values.
     */
    private function buildVersionedValues(iterable $values, int $version): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new VersionedValue($value, $version);
        }
    }
}
