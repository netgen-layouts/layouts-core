<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Generator;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Block\ContainerDefinitionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BlockNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private BlockService $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     *
     * @return array<string, mixed>
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\API\Values\Block\Block $block */
        $block = $object->getValue();
        $blockDefinition = $block->getDefinition();

        $parameters = $this->buildValues($block->getParameters());
        $placeholders = $this->buildValues($block->getPlaceholders()->getValues());

        $configuration = (function () use ($block): Generator {
            foreach ($block->getConfigs() as $configKey => $config) {
                yield $configKey => $this->buildValues($config->getParameters());
            }
        })();

        $data = [
            'id' => $block->getId()->toString(),
            'layout_id' => $block->getLayoutId()->toString(),
            'definition_identifier' => $blockDefinition->getIdentifier(),
            'name' => $block->getName(),
            'parent_position' => $block->getPosition(),
            'parameters' => $this->normalizer->normalize($parameters, $format, $context),
            'view_type' => $block->getViewType(),
            'item_view_type' => $block->getItemViewType(),
            'published' => $block->isPublished(),
            'has_published_state' => $this->blockService->hasPublishedState($block),
            'locale' => $block->getLocale(),
            'is_translatable' => $block->isTranslatable(),
            'always_available' => $block->isAlwaysAvailable(),
            'is_container' => false,
            'placeholders' => $this->normalizer->normalize($placeholders, $format, $context),
            'collections' => $this->normalizer->normalize($this->getBlockCollections($block), $format, $context),
            'config' => $this->normalizer->normalize($configuration, $format, $context),
        ];

        if ($blockDefinition instanceof ContainerDefinitionInterface) {
            $data['is_container'] = true;
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     */
    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Block;
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
     * @return \Generator<array<string, mixed>>
     */
    private function getBlockCollections(Block $block): Generator
    {
        foreach ($block->getCollections() as $identifier => $collection) {
            yield [
                'identifier' => $identifier,
                'collection_id' => $collection->getId()->toString(),
                'collection_type' => $collection->hasQuery() ? Collection::TYPE_DYNAMIC : Collection::TYPE_MANUAL,
                'offset' => $collection->getOffset(),
                'limit' => $collection->getLimit(),
            ];
        }
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
