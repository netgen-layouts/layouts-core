<?php

namespace Netgen\BlockManager\Serializer\ValueNormalizer;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
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

        $manualItems = array();
        foreach ($collection->getManualItems() as $manualItem) {
            $manualItems[] = new VersionedValue($manualItem, $object->getVersion());
        }

        $overrideItems = array();
        foreach ($collection->getOverrideItems() as $overrideItem) {
            $overrideItems[] = new VersionedValue($overrideItem, $object->getVersion());
        }

        return array(
            'id' => $collection->getId(),
            'type' => $collection->getType(),
            'name' => $collection->getName(),
            'manual_items' => $this->serializer->normalize(new ValueArray($manualItems)),
            'override_items' => $this->serializer->normalize(new ValueArray($overrideItems)),
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

        return $data->getValue() instanceof Collection && $data->getVersion() === 1;
    }
}
