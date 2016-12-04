<?php

namespace Netgen\BlockManager\Serializer\V1\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\Source\Query;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SourceQueryNormalizer implements NormalizerInterface
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
        /** @var \Netgen\BlockManager\Configuration\Source\Query $query */
        $query = $object->getValue();

        return array(
            'identifier' => $query->getIdentifier(),
            'query_type' => $query->getQueryType()->getType(),
            'default_parameters' => $query->getDefaultParameters(),
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
