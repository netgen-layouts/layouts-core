<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionItemNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\API\Values\Collection\Item $collectionItem */
        $collectionItem = $object->getValue();
        $cmsItem = $collectionItem->getCmsItem();

        $data = array(
            'id' => $collectionItem->getId(),
            'collection_id' => $collectionItem->getCollectionId(),
            'position' => $collectionItem->getPosition(),
            'type' => $collectionItem->getType(),
            'visible' => $collectionItem->isVisible(),
            'scheduled' => $collectionItem->isScheduled(),
            'value' => $cmsItem->getValue(),
            'value_type' => $cmsItem->getValueType(),
            'name' => $cmsItem->getName(),
            'cms_visible' => $cmsItem->isVisible(),
            'cms_url' => null,
        );

        if ($cmsItem instanceof NullItem) {
            return $data;
        }

        $data['cms_url'] = $this->urlGenerator->generate($cmsItem);

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
