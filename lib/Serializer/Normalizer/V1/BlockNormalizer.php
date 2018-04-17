<?php

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Serializer\SerializerAwareTrait;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

final class BlockNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\API\Values\Block\Block $block */
        $block = $object->getValue();
        $blockDefinition = $block->getDefinition();

        $parameters = [];
        foreach ($block->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = new VersionedValue($parameter, $object->getVersion());
        }

        $placeholders = [];
        foreach ($block->getPlaceholders() as $placeholder) {
            $placeholders[] = new VersionedValue($placeholder, $object->getVersion());
        }

        $isContainer = $blockDefinition instanceof ContainerDefinitionInterface;

        return [
            'id' => $block->getId(),
            'layout_id' => $block->getLayoutId(),
            'definition_identifier' => $blockDefinition->getIdentifier(),
            'name' => $block->getName(),
            'parent_position' => $block->getParentPosition(),
            'parameters' => $this->serializer->normalize($parameters, $format, $context),
            'view_type' => $block->getViewType(),
            'item_view_type' => $block->getItemViewType(),
            'published' => $block->isPublished(),
            'has_published_state' => $this->blockService->hasPublishedState($block),
            'locale' => $block->getLocale(),
            'is_translatable' => $block->isTranslatable(),
            'always_available' => $block->isAlwaysAvailable(),
            'is_container' => $isContainer,
            'is_dynamic_container' => $isContainer && $blockDefinition->isDynamicContainer(),
            'placeholders' => $this->serializer->normalize($placeholders, $format, $context),
            'collections' => $this->normalizeBlockCollections($block),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Block && $data->getVersion() === Version::API_V1;
    }

    private function normalizeBlockCollections(Block $block)
    {
        $data = [];

        foreach ($block->getCollections() as $identifier => $collection) {
            $data[] = [
                'identifier' => $identifier,
                'collection_id' => $collection->getId(),
                'collection_type' => $collection->getType(),
                'offset' => $collection->getOffset(),
                'limit' => $collection->getLimit(),
            ];
        }

        return $data;
    }
}
