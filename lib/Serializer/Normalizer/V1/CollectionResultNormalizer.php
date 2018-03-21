<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Item\UrlBuilderInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Item\UrlBuilderInterface
     */
    private $urlBuilder;

    public function __construct(UrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\Collection\Result\Result $result */
        $result = $object->getValue();
        $cmsItem = $result->getItem();
        $collectionItem = $result->getCollectionItem();

        return array(
            'id' => $collectionItem !== null ? $collectionItem->getId() : null,
            'collection_id' => $collectionItem !== null ? $collectionItem->getCollectionId() : null,
            'position' => $result->getPosition(),
            'type' => $result->getType(),
            'cms_url' => !$cmsItem instanceof NullItem ? $this->urlBuilder->getUrl($cmsItem) : null,
            'cms_visible' => $cmsItem->isVisible(),
            'value' => $cmsItem->getValue(),
            'value_type' => $cmsItem->getValueType(),
            'name' => $cmsItem->getName(),
            'visible' => $collectionItem !== null ? $collectionItem->isVisible() : true,
            'scheduled' => $collectionItem !== null ? $collectionItem->isScheduled() : false,
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Result && $data->getVersion() === Version::API_V1;
    }
}
