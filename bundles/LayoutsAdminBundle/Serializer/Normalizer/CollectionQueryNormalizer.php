<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Generator;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Query;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionQueryNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @param mixed $object
     * @param string|null $format
     *
     * @return array<string, mixed>
     */
    public function normalize($object, $format = null, array $context = []): array
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

    /**
     * @param mixed $data
     * @param string|null $format
     */
    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Query;
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

    /**
     * Builds the list of Value objects for provided list of values.
     *
     * @param iterable<object> $values
     *
     * @return \Generator<array-key, \Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value>
     */
    private function buildValues(iterable $values): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new Value($value);
        }
    }
}
