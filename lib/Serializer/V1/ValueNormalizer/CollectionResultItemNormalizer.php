<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Collection\Result\ResultItem;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CollectionResultItemNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\VersionedValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Collection\Result\ResultItem $resultItem */
        $resultItem = $object->getValue();
        $item = $resultItem->getItem();
        $collectionItem = $resultItem->getCollectionItem();

        return array(
            'id' => $collectionItem !== null ? $collectionItem->getId() : null,
            'collection_id' => $collectionItem !== null ? $collectionItem->getCollectionId() : null,
            'position' => $resultItem->getPosition(),
            'type' => $resultItem->getType(),
            'value_id' => $item->getValueId(),
            'value_type' => $item->getValueType(),
            'name' => $item->getName(),
            'visible' => $item->isVisible(),
        );
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof ResultItem && $data->getVersion() === Version::API_V1;
    }
}
