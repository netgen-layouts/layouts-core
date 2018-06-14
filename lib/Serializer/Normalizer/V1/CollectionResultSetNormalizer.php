<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Serializer\SerializerAwareTrait;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

final class CollectionResultSetNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\Collection\Result\ResultSet $resultSet */
        $resultSet = $object->getValue();

        $results = [];
        foreach ($resultSet as $result) {
            $results[] = new VersionedValue($result, $object->getVersion());
        }

        $overflowItems = [];
        foreach ($this->getOverflowItems($resultSet) as $overflowItem) {
            $overflowItems[] = new VersionedValue($overflowItem, $object->getVersion());
        }

        return [
            'items' => $this->serializer->normalize($results, $format, $context),
            'overflow_items' => $this->serializer->normalize($overflowItems, $format, $context),
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
     *
     * @param \Netgen\BlockManager\Collection\Result\ResultSet $resultSet
     *
     * @return \Netgen\BlockManager\Collection\Result\Result[]
     */
    private function getOverflowItems(ResultSet $resultSet): array
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

        $overflowItems = [];
        foreach ($resultSet->getCollection()->getItems() as $item) {
            if (!in_array($item->getPosition(), $includedPositions, true)) {
                $overflowItems[] = $item;
            }
        }

        return $overflowItems;
    }
}
