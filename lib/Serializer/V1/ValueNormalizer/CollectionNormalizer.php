<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class CollectionNormalizer extends SerializerAwareNormalizer implements NormalizerInterface
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
        /** @var \Netgen\BlockManager\API\Values\Collection\Collection $collection */
        $collection = $object->getValue();

        $items = array();
        foreach ($collection->getItems() as $item) {
            $items[] = new VersionedValue($item, $object->getVersion());
        }

        $queries = array();
        foreach ($collection->getQueries() as $query) {
            $queries[] = new VersionedValue($query, $object->getVersion());
        }

        return array(
            'id' => $collection->getId(),
            'type' => $collection->getType(),
            'shared' => $collection->isShared(),
            'name' => $collection->getName(),
            'items' => $this->serializer->normalize(new ValueList($items)),
            'queries' => $this->serializer->normalize(new ValueList($queries)),
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

        return $data->getValue() instanceof Collection && $data->getVersion() === Version::API_V1;
    }
}
