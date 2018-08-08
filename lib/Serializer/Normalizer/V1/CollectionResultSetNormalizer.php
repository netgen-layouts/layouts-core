<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Generator;
use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Serializer\Normalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultSetNormalizer extends Normalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\Collection\Result\ResultSet $resultSet */
        $resultSet = $object->getValue();

        $results = $this->buildVersionedValues($resultSet, $object->getVersion());
        $overflowItems = $this->buildVersionedValues($this->getOverflowItems($resultSet), $object->getVersion());

        return [
            'items' => $this->normalizer->normalize($results, $format, $context),
            'overflow_items' => $this->normalizer->normalize($overflowItems, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof ResultSet && $data->getVersion() === Version::API_V1;
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
     * Builds the list of VersionedValue objects for provided list of values.
     */
    private function buildVersionedValues(iterable $values, int $version): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new VersionedValue($value, $version);
        }
    }
}
