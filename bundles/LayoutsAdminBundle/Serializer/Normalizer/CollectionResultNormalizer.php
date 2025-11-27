<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Generator;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Config\ConfigList;
use Netgen\Layouts\Collection\Item\VisibilityResolverInterface;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Item\UrlType;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private VisibilityResolverInterface $visibilityResolver,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\Collection\Result\Result $result */
        $result = $data->getValue();

        $mainItem = $result->subItem instanceof CmsItemInterface ? $result->subItem : $result->item;
        $overrideItem = $result->subItem instanceof CmsItemInterface ? $result->item : null;

        $normalizedData = $this->normalizeResultItem($mainItem, $format, $context);

        $normalizedData['position'] = $result->position;
        $normalizedData['slot_id'] = null;
        $normalizedData['slot_view_type'] = null;

        if ($result->slot instanceof Slot) {
            $normalizedData['slot_id'] = $result->slot->id->toString();
            $normalizedData['slot_view_type'] = $result->slot->viewType;
        }

        if ($overrideItem instanceof CmsItemInterface) {
            $normalizedData['override_item'] = $this->normalizeResultItem($overrideItem, $format, $context);
        }

        return $normalizedData;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Result;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }

    /**
     * Normalizes the provided result item into an array.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    private function normalizeResultItem(CmsItemInterface $resultItem, ?string $format = null, array $context = []): array
    {
        $collectionItem = null;
        $cmsItem = $resultItem;
        $itemViewType = null;
        $isDynamic = true;

        if ($resultItem instanceof ManualItem) {
            $collectionItem = $resultItem->collectionItem;
            $cmsItem = $collectionItem->cmsItem;
            $itemViewType = $collectionItem->viewType;
            $isDynamic = false;
        }

        $configuration = (function () use ($collectionItem): Generator {
            $itemConfigs = $collectionItem?->getConfigs() ?? new ConfigList();
            foreach ($itemConfigs as $configKey => $config) {
                yield $configKey => $this->buildValues($config->getParameters());
            }
        })();

        $normalizedData = [
            'id' => $collectionItem?->id->toString(),
            'collection_id' => $collectionItem?->collectionId->toString(),
            'visible' => $collectionItem !== null ?
                $this->visibilityResolver->isVisible($collectionItem) :
                true,
            'is_dynamic' => $isDynamic,
            'value' => $cmsItem->value,
            'value_type' => $cmsItem->valueType,
            'item_view_type' => $itemViewType,
            'name' => $cmsItem->name,
            'cms_visible' => $cmsItem->isVisible,
            'cms_url' => '',
            'config' => $this->normalizer->normalize($configuration, $format, $context),
        ];

        try {
            $normalizedData['cms_url'] = $this->urlGenerator->generate($cmsItem, UrlType::Admin);
        } catch (ItemException) {
            // Do nothing
        }

        return $normalizedData;
    }

    /**
     * Builds the list of Value objects for provided list of values.
     *
     * @param iterable<object> $values
     *
     * @return \Generator<\Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value>
     */
    private function buildValues(iterable $values): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new Value($value);
        }
    }
}
