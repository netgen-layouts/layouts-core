<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewRendererInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LayoutViewNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer
     */
    protected $blockViewNormalizer;

    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer $blockViewNormalizer
     * @param \Netgen\BlockManager\View\ViewRendererInterface $viewRenderer
     */
    public function __construct(
        ViewBuilderInterface $viewBuilder,
        BlockViewNormalizer $blockViewNormalizer,
        ViewRendererInterface $viewRenderer
    ) {
        $this->viewBuilder = $viewBuilder;
        $this->blockViewNormalizer = $blockViewNormalizer;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\View\LayoutViewInterface $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $layout = $object->getLayout();

        return array(
            'id' => $layout->getId(),
            'parent_id' => $layout->getParentId(),
            'identifier' => $layout->getIdentifier(),
            'created_at' => $layout->getCreated(),
            'updated_at' => $layout->getModified(),
            'name' => $layout->getName(),
            'zones' => $object->getParameter('zones'),
            'blocks' => $this->normalizeBlocks($object),
            'positions' => $object->getParameter('positions'),
            'html' => $this->viewRenderer->renderView($object),
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
        return $data instanceof LayoutViewInterface;
    }

    /**
     * Returns the data for blocks contained within the layout.
     *
     * @param \Netgen\BlockManager\View\LayoutViewInterface $layoutView
     *
     * @return array
     */
    protected function normalizeBlocks(LayoutViewInterface $layoutView)
    {
        $blocks = array();

        foreach ($layoutView->getLayout()->getZones() as $zone) {
            $blocks = array_merge($blocks, $zone->getBlocks());
        }

        $normalizedBlocks = array();
        foreach ($blocks as $block) {
            $normalizedBlocks[] = $this->blockViewNormalizer->normalize(
                $this->viewBuilder->buildView($block, $layoutView->getContext())
            );
        }

        return $normalizedBlocks;
    }
}
