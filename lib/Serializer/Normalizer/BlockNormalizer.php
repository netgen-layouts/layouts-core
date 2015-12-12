<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Netgen\BlockManager\View\ViewRendererInterface;

class BlockNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\BlockManager\View\ViewRendererInterface $viewRenderer
     */
    public function __construct(ViewBuilderInterface $viewBuilder, ViewRendererInterface $viewRenderer)
    {
        $this->viewBuilder = $viewBuilder;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\SerializableValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $block = $object->getValue();

        $blockView = $this->viewBuilder->buildView(
            $block,
            ViewInterface::CONTEXT_API,
            array('api_version' => $object->getVersion())
        );

        return array(
            'id' => $block->getId(),
            'definition_identifier' => $block->getDefinitionIdentifier(),
            'name' => $block->getName(),
            'zone_id' => $block->getZoneId(),
            'parameters' => $block->getParameters(),
            'view_type' => $block->getViewType(),
            'html' => $this->viewRenderer->renderView($blockView),
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
        if (!$data instanceof SerializableValue) {
            return false;
        }

        return $data->getValue() instanceof Block && $data->getVersion() === 1;
    }
}
