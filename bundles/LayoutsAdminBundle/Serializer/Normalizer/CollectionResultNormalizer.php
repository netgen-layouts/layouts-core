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
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\Collection\Result\Result $result */
        $result = $object->getValue();
        $subItem = $result->getSubItem();

        $mainItem = $subItem instanceof CmsItemInterface ? $subItem : $result->getItem();
        $overrideItem = $subItem instanceof CmsItemInterface ? $result->getItem() : null;

        $data = $this->normalizeResultItem($mainItem, $format, $context);

        $data['position'] = $result->getPosition();
        $data['slot_id'] = null;
        $data['slot_view_type'] = null;

        $slot = $result->getSlot();
        if ($slot instanceof Slot) {
            $data['slot_id'] = $slot->getId()->toString();
            $data['slot_view_type'] = $slot->getViewType();
        }

        if ($overrideItem instanceof CmsItemInterface) {
            $data['override_item'] = $this->normalizeResultItem($overrideItem, $format, $context);
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
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
            $collectionItem = $resultItem->getCollectionItem();
            $cmsItem = $collectionItem->getCmsItem();
            $itemViewType = $collectionItem->getViewType();
            $isDynamic = false;
        }

        $configuration = (function () use ($collectionItem): Generator {
            $itemConfigs = $collectionItem?->getConfigs() ?? new ConfigList();
            foreach ($itemConfigs as $configKey => $config) {
                yield $configKey => $this->buildValues($config->getParameters());
            }
        })();

        $data = [
            'id' => $collectionItem?->getId()->toString(),
            'collection_id' => $collectionItem?->getCollectionId()->toString(),
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
            $data['cms_url'] = $this->urlGenerator->generate($cmsItem, UrlType::Admin);
        } catch (ItemException) {
            // Do nothing
        }

        return $data;
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
