<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\Slot;
use Netgen\BlockManager\Item\ItemInterface;
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

        $mainItem = $result->getSubItem() instanceof ItemInterface ? $result->getSubItem() : $result->getItem();
        $overrideItem = $result->getSubItem() instanceof ItemInterface ? $result->getItem() : null;

        $data = $this->normalizeResultItem($mainItem);
        $data['position'] = $result->getPosition();

        if ($overrideItem instanceof ItemInterface) {
            $data['override_item'] = $this->normalizeResultItem($overrideItem);
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Result && $data->getVersion() === Version::API_V1;
    }

    /**
     * Normalizes the provided result item into an array.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $resultItem
     *
     * @return array
     */
    private function normalizeResultItem(ItemInterface $resultItem)
    {
        $itemUrl = null;
        $collectionItem = null;

        if ($resultItem instanceof ManualItem) {
            $collectionItem = $resultItem->getCollectionItem();
            if (!$resultItem->getCollectionItem()->getCmsItem() instanceof NullItem) {
                $itemUrl = $this->urlBuilder->getUrl($resultItem->getCollectionItem()->getCmsItem());
            }
        } elseif (!$resultItem instanceof Slot) {
            $itemUrl = $this->urlBuilder->getUrl($resultItem);
        }

        return array(
            'id' => $collectionItem !== null ? $collectionItem->getId() : null,
            'collection_id' => $collectionItem !== null ? $collectionItem->getCollectionId() : null,
            'visible' => $collectionItem !== null ? $collectionItem->isVisible() : true,
            'scheduled' => $collectionItem !== null ? $collectionItem->isScheduled() : false,
            'is_dynamic' => $resultItem instanceof ManualItem ? false : true,
            'value' => $resultItem->getValue(),
            'value_type' => $resultItem->getValueType(),
            'name' => $resultItem->getName(),
            'cms_visible' => $resultItem->isVisible(),
            'cms_url' => $itemUrl,
        );
    }
}
