<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewRendererInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LayoutViewNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

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
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\BlockManager\Serializer\Normalizer\BlockViewNormalizer $blockViewNormalizer
     * @param \Netgen\BlockManager\View\ViewRendererInterface $viewRenderer
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ViewBuilderInterface $viewBuilder,
        BlockViewNormalizer $blockViewNormalizer,
        ViewRendererInterface $viewRenderer
    ) {
        $this->configuration = $configuration;
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
            'zones' => $this->getZones($layout),
            'blocks' => $this->normalizeBlocks($object),
            'positions' => $this->getBlockPositions($layout),
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

    /**
     * Returns the array with layout zones.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return array
     */
    protected function getZones(Layout $layout)
    {
        $zones = array();
        $layoutConfig = $this->configuration->getLayoutConfig($layout->getIdentifier());

        foreach ($layout->getZones() as $zoneIdentifier => $zone) {
            $allowedBlocks = true;

            if (isset($layoutConfig['zones'][$zoneIdentifier])) {
                $zoneConfig = $layoutConfig['zones'][$zoneIdentifier];
                if (!empty($zoneConfig['allowed_blocks'])) {
                    $allowedBlocks = $zoneConfig['allowed_blocks'];
                }
            }

            $zones[] = array(
                'identifier' => $zoneIdentifier,
                'allowed_blocks' => $allowedBlocks,
            );
        }

        return $zones;
    }

    /**
     * Returns the array with block positions inside zones.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return array
     */
    protected function getBlockPositions(Layout $layout)
    {
        $positions = array();

        foreach ($layout->getZones() as $zoneIdentifier => $zone) {
            $positions[] = array(
                'zone' => $zoneIdentifier,
                'blocks' => array_map(
                    function (Block $block) {
                        return $block->getId();
                    },
                    $zone->getBlocks()
                ),
            );
        }

        return $positions;
    }
}
