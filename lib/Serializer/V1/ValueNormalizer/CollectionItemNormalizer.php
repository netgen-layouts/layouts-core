<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Exception;

class CollectionItemNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface
     */
    protected $resultValueBuilder;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\ResultGenerator\ResultValueBuilderInterface $resultValueBuilder
     */
    public function __construct(ResultValueBuilderInterface $resultValueBuilder)
    {
        $this->resultValueBuilder = $resultValueBuilder;
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
            $resultValue = $this->resultValueBuilder->buildFromItem($item);
            $data['name'] = $resultValue->name;
            $data['visible'] = $resultValue->isVisible;
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
