<?php

namespace Netgen\BlockManager\Serializer\V1\ValueNormalizer;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CollectionReferenceNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     */
    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
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
        /** @var \Netgen\BlockManager\API\Values\Page\CollectionReference $collectionReference */
        $collectionReference = $object->getValue();

        if ($collectionReference->getCollectionStatus() === Collection::STATUS_PUBLISHED) {
            $collection = $this->collectionService->loadCollection(
                $collectionReference->getCollectionId()
            );
        } else {
            $collection = $this->collectionService->loadCollectionDraft(
                $collectionReference->getCollectionId()
            );
        }

        return array(
            'id' => $collection->getId(),
            'type' => $collection->getType(),
            'name' => $collection->getName(),
            'identifier' => $collectionReference->getIdentifier(),
            'offset' => $collectionReference->getOffset(),
            'limit' => $collectionReference->getLimit(),
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

        return $data->getValue() instanceof CollectionReference && $data->getVersion() === Version::API_V1;
    }
}
