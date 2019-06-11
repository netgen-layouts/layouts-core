<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer;

use Generator;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionQueryNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\API\Values\Collection\Query $query */
        $query = $object->getValue();

        $parameters = $this->buildValues($query->getParameters());

        return [
            'id' => $query->getId()->toString(),
            'collection_id' => $query->getCollectionId()->toString(),
            'type' => $query->getQueryType()->getType(),
            'locale' => $query->getLocale(),
            'is_translatable' => $query->isTranslatable(),
            'always_available' => $query->isAlwaysAvailable(),
            'parameters' => $this->normalizer->normalize($parameters, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Query;
    }

    /**
     * Builds the list of Value objects for provided list of values.
     */
    private function buildValues(iterable $values): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new Value($value);
        }
    }
}
