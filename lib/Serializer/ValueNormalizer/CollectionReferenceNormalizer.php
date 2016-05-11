<?php

namespace Netgen\BlockManager\Serializer\ValueNormalizer;

use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
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
        /** @var \Netgen\BlockManager\API\Values\Page\CollectionReference $collection */
        $collection = $object->getValue();

        return array(
            'block_id' => $collection->getBlockId(),
            'collection_id' => $collection->getCollectionId(),
            'identifier' => $collection->getIdentifier(),
            'offset' => $collection->getOffset(),
            'limit' => $collection->getLimit(),
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

        return $data->getValue() instanceof CollectionReference && $data->getVersion() === 1;
    }
}
