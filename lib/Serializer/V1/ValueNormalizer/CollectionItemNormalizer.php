<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Value\ValueBuilderInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Exception;

class CollectionItemNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Value\ValueBuilderInterface
     */
    protected $valueBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Value\ValueBuilderInterface $valueBuilder
     */
    public function __construct(ValueBuilderInterface $valueBuilder)
    {
        $this->valueBuilder = $valueBuilder;
    }

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
        /** @var \Netgen\BlockManager\API\Values\Collection\Item $item */
        $item = $object->getValue();

        $data = array(
            'id' => $item->getId(),
            'collection_id' => $item->getCollectionId(),
            'position' => $item->getPosition(),
            'type' => $item->getType(),
            'value_id' => $item->getValueId(),
            'value_type' => $item->getValueType(),
            'name' => null,
            'visible' => null,
        );

        try {
            $value = $this->valueBuilder->build($item->getValueId(), $item->getValueType());
            $data['name'] = $value->getName();
            $data['visible'] = $value->isVisible();
        } catch (Exception $e) {
            // Do nothing
        }

        return $data;
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

        return $data->getValue() instanceof Item && $data->getVersion() === Version::API_V1;
    }
}
