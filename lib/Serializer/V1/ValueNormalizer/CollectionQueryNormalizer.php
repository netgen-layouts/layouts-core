<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\BlockManager\Traits\SerializerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

class CollectionQueryNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

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
        /** @var \Netgen\BlockManager\API\Values\Collection\Query $query */
        $query = $object->getValue();

        $parameters = array();
        foreach ($query->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = new VersionedValue($parameter, $object->getVersion());
        }

        return array(
            'id' => $query->getId(),
            'collection_id' => $query->getCollectionId(),
            'type' => $query->getQueryType()->getType(),
            'parameters' => $this->serializer->normalize($parameters, $format, $context),
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

        return $data->getValue() instanceof Query && $data->getVersion() === Version::API_V1;
    }
}
