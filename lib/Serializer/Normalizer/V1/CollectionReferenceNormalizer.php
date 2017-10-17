<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionReferenceNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\API\Values\Block\CollectionReference $collectionReference */
        $collectionReference = $object->getValue();

        $collection = $collectionReference->getCollection();

        return array(
            'identifier' => $collectionReference->getIdentifier(),
            'collection_id' => $collection->getId(),
            'collection_type' => $collection->getType(),
            'offset' => $collection->getOffset(),
            'limit' => $collection->getLimit(),
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof CollectionReference && $data->getVersion() === Version::API_V1;
    }
}
