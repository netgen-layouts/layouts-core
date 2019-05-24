<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer\V1;

use Generator;
use Netgen\Layouts\Collection\Item\VisibilityResolverInterface;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @var \Netgen\Layouts\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Netgen\Layouts\Collection\Item\VisibilityResolverInterface
     */
    private $visibilityResolver;

    public function __construct(UrlGeneratorInterface $urlGenerator, VisibilityResolverInterface $visibilityResolver)
    {
        $this->urlGenerator = $urlGenerator;
        $this->visibilityResolver = $visibilityResolver;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\Collection\Result\Result $result */
        $result = $object->getValue();

        $mainItem = $result->getSubItem() instanceof CmsItemInterface ? $result->getSubItem() : $result->getItem();
        $overrideItem = $result->getSubItem() instanceof CmsItemInterface ? $result->getItem() : null;

        $data = $this->normalizeResultItem($mainItem, $object->getVersion(), $format, $context);
        $data['position'] = $result->getPosition();

        if ($overrideItem instanceof CmsItemInterface) {
            $data['override_item'] = $this->normalizeResultItem($overrideItem, $object->getVersion(), $format, $context);
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
    private function normalizeResultItem(CmsItemInterface $resultItem, int $version, ?string $format = null, array $context = []): array
    {
        $collectionItem = null;
        $cmsItem = $resultItem;
        $itemViewType = null;
        $isDynamic = true;

        if ($resultItem instanceof ManualItem) {
            $collectionItem = $resultItem->getCollectionItem();
            $cmsItem = $collectionItem->getCmsItem();
            $itemViewType = $collectionItem->getViewType();
            $isDynamic = false;
        }

        $configuration = (function () use ($collectionItem, $version): Generator {
            $itemConfigs = $collectionItem !== null ? $collectionItem->getConfigs() : [];
            foreach ($itemConfigs as $configKey => $config) {
                yield $configKey => $this->buildVersionedValues($config->getParameters(), $version);
            }
        })();

        $data = [
            'id' => $collectionItem !== null ? $collectionItem->getId()->toString() : null,
            'collection_id' => $collectionItem !== null ? $collectionItem->getCollectionId()->toString() : null,
            'visible' => $collectionItem !== null ?
                $this->visibilityResolver->isVisible($collectionItem) :
                true,
            'is_dynamic' => $isDynamic,
            'value' => $cmsItem->getValue(),
            'value_type' => $cmsItem->getValueType(),
            'item_view_type' => $itemViewType,
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
