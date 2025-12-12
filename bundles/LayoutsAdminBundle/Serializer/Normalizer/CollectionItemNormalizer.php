<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Item\VisibilityResolverInterface;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Item\UrlType;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionItemNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
        /** @var \Netgen\Layouts\API\Values\Collection\Item $collectionItem */
        $collectionItem = $data->value;
        $cmsItem = $collectionItem->cmsItem;

        $configuration = (function () use ($collectionItem): iterable {
            foreach ($collectionItem->configs as $configKey => $config) {
                yield $configKey => $this->buildValues($config->parameters);
            }
        })();

        $normalizedData = [
            'id' => $collectionItem->id->toString(),
            'collection_id' => $collectionItem->collectionId->toString(),
            'position' => $collectionItem->position,
            'visible' => $this->visibilityResolver->isVisible($collectionItem),
            'value' => $cmsItem->value,
            'value_type' => $cmsItem->valueType,
            'item_view_type' => $collectionItem->viewType,
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

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->value instanceof Item;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }

    /**
     * Builds the list of Value objects for provided list of values.
     *
     * @param iterable<object> $values
     *
     * @return iterable<\Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value>
     */
    private function buildValues(iterable $values): iterable
    {
        foreach ($values as $key => $value) {
            yield $key => new Value($value);
        }
    }
}
