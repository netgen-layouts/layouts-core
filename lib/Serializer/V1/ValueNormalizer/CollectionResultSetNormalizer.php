<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class CollectionResultSetNormalizer extends SerializerAwareNormalizer implements NormalizerInterface
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
        /** @var \Netgen\BlockManager\Collection\Result\ResultSet $resultSet */
        $resultSet = $object->getValue();

        $results = array();
        foreach ($resultSet->getResults() as $result) {
            $results[] = new VersionedValue($result, $object->getVersion());
        }

        return array(
            'items' => $this->serializer->normalize(new ValueList($results), $format, $context),
            'item_count' => $resultSet->getTotalCount(),
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

        return $data->getValue() instanceof ResultSet && $data->getVersion() === Version::API_V1;
    }
}
