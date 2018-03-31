<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Item\UrlBuilderInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionItemNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\UrlBuilderInterface
     */
    private $urlBuilder;

    public function __construct(ItemLoaderInterface $itemLoader, UrlBuilderInterface $urlBuilder)
    {
        $this->itemLoader = $itemLoader;
        $this->urlBuilder = $urlBuilder;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\API\Values\Collection\Item $item */
        $collectionItem = $object->getValue();

        $data = array(
            'id' => $collectionItem->getId(),
            'collection_id' => $collectionItem->getCollectionId(),
            'position' => $collectionItem->getPosition(),
            'type' => $collectionItem->getType(),
            'value' => $collectionItem->getValue(),
            'value_type' => $collectionItem->getValueType(),
            'visible' => $collectionItem->isVisible(),
            'scheduled' => $collectionItem->isScheduled(),
            'name' => null,
            'cms_url' => null,
            'cms_visible' => true,
        );

        $cmsItem = $this->itemLoader->load(
            $collectionItem->getValue(),
            $collectionItem->getValueType()
        );

        if ($cmsItem instanceof NullItem) {
            return $data;
        }

        $data['name'] = $cmsItem->getName();
        $data['cms_url'] = $this->urlBuilder->getUrl($cmsItem);
        $data['cms_visible'] = $cmsItem->isVisible();

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Item && $data->getVersion() === Version::API_V1;
    }
}
