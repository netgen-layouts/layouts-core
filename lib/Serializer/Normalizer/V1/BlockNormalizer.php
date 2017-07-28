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

class BlockNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     */
    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\VersionedValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var \Netgen\BlockManager\API\Values\Block\Block $block */
        $block = $object->getValue();
        $blockDefinition = $block->getDefinition();

        $parameters = array();
        foreach ($block->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = new VersionedValue($parameter, $object->getVersion());
        }

        $placeholders = array();
        foreach ($block->getPlaceholders() as $placeholder) {
            $placeholders[] = new VersionedValue($placeholder, $object->getVersion());
        }

        $isContainer = $blockDefinition instanceof ContainerDefinitionInterface;

        return array(
            'id' => $block->getId(),
            'layout_id' => $block->getLayoutId(),
            'definition_identifier' => $blockDefinition->getIdentifier(),
            'name' => $block->getName(),
            'parameters' => $this->serializer->normalize($parameters, $format, $context),
            'view_type' => $block->getViewType(),
            'item_view_type' => $block->getItemViewType(),
            'published' => $block->isPublished(),
            'has_published_state' => $this->blockService->hasPublishedState($block),
            'locale' => $block->getTranslation()->getLocale(),
            'is_translatable' => $block->isTranslatable(),
            'always_available' => $block->isAlwaysAvailable(),
            'is_container' => $isContainer,
            'is_dynamic_container' => $isContainer && $blockDefinition->isDynamicContainer(),
            'placeholders' => $this->serializer->normalize($placeholders, $format, $context),
        );
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Block && $data->getVersion() === Version::API_V1;
    }
}
