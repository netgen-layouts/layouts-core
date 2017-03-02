<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CollectionReferenceNormalizer implements NormalizerInterface
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
        /** @var \Netgen\BlockManager\API\Values\Block\CollectionReference $collectionReference */
        $collectionReference = $object->getValue();

        $block = $collectionReference->getBlock();
        $collection = $collectionReference->getCollection();

        return array(
            'identifier' => $collectionReference->getIdentifier(),
            'block_id' => $block->getId(),
            'collection_id' => $collection->getId(),
            'collection_type' => $collection->getType(),
            'collection_shared' => $collection->isShared(),
            'collection_name' => $collection->getName(),
            'offset' => $collectionReference->getOffset(),
            'limit' => $collectionReference->getLimit(),
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

        return $data->getValue() instanceof CollectionReference && $data->getVersion() === Version::API_V1;
    }
}
