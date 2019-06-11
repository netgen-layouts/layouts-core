<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer;

use Generator;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Serializer\Values\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultSetNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\Collection\Result\ResultSet $resultSet */
        $resultSet = $object->getValue();

        $results = $this->buildValues($resultSet);
        $overflowItems = $this->buildValues($this->getOverflowItems($resultSet));

        return [
            'items' => $this->normalizer->normalize($results, $format, $context),
            'overflow_items' => $this->normalizer->normalize($overflowItems, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof ResultSet;
    }

    /**
     * Returns all items from the collection which are overflown. Overflown items
     * are those NOT included in the provided result set, as defined by collection
     * offset and limit.
     */
    private function getOverflowItems(ResultSet $resultSet): Generator
    {
        $includedPositions = [];
        foreach ($resultSet->getResults() as $result) {
            if ($result->getItem() instanceof ManualItem) {
                $includedPositions[] = $result->getItem()->getCollectionItem()->getPosition();
            }

            if ($result->getSubItem() instanceof ManualItem) {
                $includedPositions[] = $result->getSubItem()->getCollectionItem()->getPosition();
            }
        }

        foreach ($resultSet->getCollection()->getItems() as $item) {
            if (!in_array($item->getPosition(), $includedPositions, true)) {
                yield $item;
            }
        }
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
