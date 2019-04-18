<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Generator;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Exception\Item\ItemException;
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

        $configuration = (function () use ($collectionItem, $object): Generator {
            foreach ($collectionItem->getConfigs() as $configKey => $config) {
                yield $configKey => $this->buildVersionedValues($config->getParameters(), $object->getVersion());
            }
        })();

        $data = [
            'id' => $collectionItem->getId(),
            'collection_id' => $collectionItem->getCollectionId(),
            'position' => $collectionItem->getPosition(),
            'visible' => $this->visibilityResolver->isVisible($collectionItem),
            'value' => $cmsItem->getValue(),
            'value_type' => $cmsItem->getValueType(),
            'name' => $cmsItem->getName(),
            'cms_visible' => $cmsItem->isVisible(),
            'cms_url' => '',
            'config' => $this->normalizer->normalize($configuration, $format, $context),
        ];

        try {
            $data['cms_url'] = $this->urlGenerator->generate($cmsItem);
        } catch (ItemException $e) {
            // Do nothing
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Item && $data->getVersion() === Version::API_V1;
    }

    /**
     * Builds the list of VersionedValue objects for provided list of values.
     */
    private function buildVersionedValues(iterable $values, int $version): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new VersionedValue($value, $version);
        }
    }
}
