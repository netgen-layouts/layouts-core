<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Netgen\BlockManager\Serializer\Normalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionItemNormalizer extends Normalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityResolverInterface
     */
    private $visibilityResolver;

    public function __construct(UrlGeneratorInterface $urlGenerator, VisibilityResolverInterface $visibilityResolver)
    {
        $this->urlGenerator = $urlGenerator;
        $this->visibilityResolver = $visibilityResolver;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\API\Values\Collection\Item $collectionItem */
        $collectionItem = $object->getValue();
        $cmsItem = $collectionItem->getCmsItem();

        $configuration = [];
        foreach ($collectionItem->getConfigs() as $configKey => $config) {
            foreach ($config->getParameters() as $parameter) {
                $configuration[$configKey][$parameter->getName()] = new VersionedValue($parameter, $object->getVersion());
            }
        }

        return [
            'id' => $collectionItem->getId(),
            'collection_id' => $collectionItem->getCollectionId(),
            'position' => $collectionItem->getPosition(),
            'type' => $collectionItem->getType(),
            'visible' => $this->visibilityResolver->isVisible($collectionItem),
            'scheduled' => false,
            'value' => $cmsItem->getValue(),
            'value_type' => $cmsItem->getValueType(),
            'name' => $cmsItem->getName(),
            'cms_visible' => $cmsItem->isVisible(),
            'cms_url' => $this->urlGenerator->generate($cmsItem),
            'config' => $this->normalizer->normalize($configuration, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Item && $data->getVersion() === Version::API_V1;
    }
}
