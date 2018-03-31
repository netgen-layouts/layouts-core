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
        $cmsItem = $result->getItem();

        $data = $this->normalizeCmsItem($cmsItem);
        $data['position'] = $result->getPosition();

        if ($result->getSubItem() instanceof ItemInterface) {
            $data['sub_item'] = $this->normalizeCmsItem($result->getSubItem());
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
     * Normalizes the provided CMS item into an array.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $cmsItem
     *
     * @return array
     */
    private function normalizeCmsItem(ItemInterface $cmsItem)
    {
        $itemUrl = null;
        $collectionItem = null;

        if ($cmsItem instanceof ManualItem) {
            $collectionItem = $cmsItem->getCollectionItem();
            if (!$cmsItem->getInnerItem() instanceof NullItem) {
                $itemUrl = $this->urlBuilder->getUrl($cmsItem->getInnerItem());
            }
        } elseif (!$cmsItem instanceof Slot) {
            $itemUrl = $this->urlBuilder->getUrl($cmsItem);
        }

        return array(
            'id' => $collectionItem !== null ? $collectionItem->getId() : null,
            'collection_id' => $collectionItem !== null ? $collectionItem->getCollectionId() : null,
            'type' => $cmsItem instanceof ManualItem ? Result::TYPE_MANUAL : Result::TYPE_DYNAMIC,
            'value' => $cmsItem->getValue(),
            'value_type' => $cmsItem->getValueType(),
            'visible' => $collectionItem !== null ? $collectionItem->isVisible() : true,
            'scheduled' => $collectionItem !== null ? $collectionItem->isScheduled() : false,
            'name' => $cmsItem->getName(),
            'cms_url' => $itemUrl,
            'cms_visible' => $cmsItem->isVisible(),
        );
    }
}
