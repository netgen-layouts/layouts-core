<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Collection\Result\ManualItem;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\Slot;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\Collection\Result\Result $result */
        $result = $object->getValue();

        $mainItem = $result->getSubItem() instanceof CmsItemInterface ? $result->getSubItem() : $result->getItem();
        $overrideItem = $result->getSubItem() instanceof CmsItemInterface ? $result->getItem() : null;

        $data = $this->normalizeResultItem($mainItem);
        $data['position'] = $result->getPosition();

        if ($overrideItem instanceof CmsItemInterface) {
            $data['override_item'] = $this->normalizeResultItem($overrideItem);
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Result && $data->getVersion() === Version::API_V1;
    }

    /**
     * Normalizes the provided result item into an array.
     */
    private function normalizeResultItem(CmsItemInterface $resultItem): array
    {
        $itemUrl = null;
        $collectionItem = null;

        if ($resultItem instanceof ManualItem) {
            $collectionItem = $resultItem->getCollectionItem();
            $itemUrl = $this->urlGenerator->generate($resultItem->getCollectionItem()->getCmsItem());
        } elseif (!$resultItem instanceof Slot) {
            $itemUrl = $this->urlGenerator->generate($resultItem);
        }

        return [
            'id' => $collectionItem !== null ? $collectionItem->getId() : null,
            'collection_id' => $collectionItem !== null ? $collectionItem->getCollectionId() : null,
            'visible' => $collectionItem !== null ? $collectionItem->isVisible() : true,
            'scheduled' => $collectionItem !== null ? $collectionItem->isScheduled() : false,
            'is_dynamic' => !$resultItem instanceof ManualItem,
            'value' => $resultItem->getValue(),
            'value_type' => $resultItem->getValueType(),
            'name' => $resultItem->getName(),
            'cms_visible' => $resultItem->isVisible(),
            'cms_url' => $itemUrl,
        ];
    }
}
