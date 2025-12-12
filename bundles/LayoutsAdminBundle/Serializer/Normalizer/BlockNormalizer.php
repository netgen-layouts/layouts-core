<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\CollectionType;
use Netgen\Layouts\Block\ContainerDefinitionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function __construct(
        private BlockService $blockService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $block */
        $block = $data->value;
        $blockDefinition = $block->definition;

        $parameters = $this->buildValues($block->parameters);
        $placeholders = $this->buildValues($block->placeholders->getValues());

        $configuration = (function () use ($block): iterable {
            foreach ($block->configs as $configKey => $config) {
                yield $configKey => $this->buildValues($config->parameters);
            }
        })();

        $normalizedData = [
            'id' => $block->id->toString(),
            'layout_id' => $block->layoutId->toString(),
            'definition_identifier' => $blockDefinition->identifier,
            'name' => $block->name,
            'parent_position' => $block->position,
            'parameters' => $this->normalizer->normalize($parameters, $format, $context),
            'view_type' => $block->viewType,
            'item_view_type' => $block->itemViewType,
            'published' => $block->isPublished,
            'has_published_state' => $this->blockService->hasPublishedState($block),
            'locale' => $block->locale,
            'is_translatable' => $block->isTranslatable,
            'always_available' => $block->isAlwaysAvailable,
            'is_container' => false,
            'placeholders' => $this->normalizer->normalize($placeholders, $format, $context),
            'collections' => $this->normalizer->normalize($this->getBlockCollections($block), $format, $context),
            'config' => $this->normalizer->normalize($configuration, $format, $context),
        ];

        if ($blockDefinition instanceof ContainerDefinitionInterface) {
            $normalizedData['is_container'] = true;
        }

        return $normalizedData;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->value instanceof Block;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }

    /**
     * @return iterable<array<string, mixed>>
     */
    private function getBlockCollections(Block $block): iterable
    {
        foreach ($block->collections as $identifier => $collection) {
            yield [
                'identifier' => $identifier,
                'collection_id' => $collection->id->toString(),
                'collection_type' => match ($collection->collectionType) {
                    CollectionType::Manual => 0,
                    CollectionType::Dynamic => 1,
                },
                'offset' => $collection->offset,
                'limit' => $collection->limit,
            ];
        }
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
