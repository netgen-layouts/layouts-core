<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ItemLoaderInterface;
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
        $item = $object->getValue();

        $data = array(
            'id' => $item->getId(),
            'collection_id' => $item->getCollectionId(),
            'position' => $item->getPosition(),
            'type' => $item->getType(),
            'value' => $item->getValue(),
            'value_type' => $item->getValueType(),
            'visible' => $item->isVisible(),
            'scheduled' => $item->isScheduled(),
            'name' => null,
            'cms_url' => null,
            'cms_visible' => true,
        );

        try {
            $value = $this->itemLoader->load($item->getValue(), $item->getValueType());
        } catch (ItemException $e) {
            return $data;
        }

        $data['name'] = $value->getName();
        $data['cms_url'] = $this->urlBuilder->getUrl($value);
        $data['cms_visible'] = $value->isVisible();

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
