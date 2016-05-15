<?php

namespace Netgen\BlockManager\Serializer\ConfigurationNormalizer;

use Netgen\BlockManager\Configuration\Source\Query;
use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class SourceNormalizer extends SerializerAwareNormalizer implements NormalizerInterface
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
        /** @var \Netgen\BlockManager\Configuration\Source\Source $source */
        $source = $object->getValue();

        $queries = array_map(
            function (Query $query) use ($object) {
                return new VersionedValue($query, $object->getVersion());
            },
            $source->getQueries()
        );

        return array(
            'identifier' => $source->getIdentifier(),
            'name' => $source->getName(),
            'queries' => $this->serializer->normalize(new ValueArray($queries)),
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

        return $data->getValue() instanceof Source && $data->getVersion() === 1;
    }
}
