<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\View\SerializableView;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Netgen\BlockManager\View\ViewRendererInterface;
use Netgen\BlockManager\View\BlockViewInterface;

class BlockViewNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewRendererInterface $viewRenderer
     */
    public function __construct(ViewRendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\View\SerializableView $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $blockView = $object->view;
        $block = $blockView->getBlock();

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
        if (!$data instanceof SerializableView) {
            return false;
        }

        return $data->view instanceof BlockViewInterface;
    }
}
