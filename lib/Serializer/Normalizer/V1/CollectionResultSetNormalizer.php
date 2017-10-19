<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Serializer\SerializerAwareTrait;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

final class CollectionResultSetNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Collection\Result\ResultSet $resultSet */
        $resultSet = $object->getValue();

        $results = array();
        foreach ($resultSet as $result) {
            $results[] = new VersionedValue($result, $object->getVersion());
        }

        return array(
            'items' => $this->serializer->normalize($results, $format, $context),
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof ResultSet && $data->getVersion() === Version::API_V1;
    }
}
