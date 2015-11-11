<?php

namespace Netgen\BlockManager\Normalizer;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutViewInterface;
use Netgen\BlockManager\View\Renderer\ViewRenderer;

class LayoutViewNormalizer extends LayoutNormalizer
{
    /**
     * @var \Netgen\BlockManager\Normalizer\BlockNormalizer
     */
    protected $blockNormalizer;

    /**
     * @var \Netgen\BlockManager\View\Renderer\ViewRenderer
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param array $layoutConfig
     * @param \Netgen\BlockManager\Normalizer\BlockNormalizer $blockNormalizer
     * @param \Netgen\BlockManager\View\Renderer\ViewRenderer $viewRenderer
     */
    public function __construct(array $layoutConfig, BlockNormalizer $blockNormalizer, ViewRenderer $viewRenderer)
    {
        parent::__construct($layoutConfig);

        $this->blockNormalizer = $blockNormalizer;
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

        $data = parent::normalize($layout);

        $data['zones'] = $this->getZones($layout);
        $data['blocks'] = $this->getBlocks($object);
        $data['positions'] = $this->getBlockPositions($object);
        $data['html'] = $this->viewRenderer->renderView($object);

        return $data;
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
     * Returns the data for zones contained within the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return array
     */
    protected function getZones(Layout $layout)
    {
        $zones = array();

        foreach ($layout->getZones() as $zone) {
            $allowedBlocks = true;
            $zoneIdentifier = $zone->getIdentifier();
            $zoneConfig = $this->layoutConfig[$layout->getIdentifier()]['zones'][$zoneIdentifier];

            if (!empty($zoneConfig['allowed_blocks'])) {
                $allowedBlocks = $zoneConfig['allowed_blocks'];
            }

            $zones[] = array(
                'identifier' => $zoneIdentifier,
                'accepts' => $allowedBlocks,
            );
        }

        return $zones;
    }

    /**
     * Returns the data for blocks contained within the layout.
     *
     * @param \Netgen\BlockManager\View\LayoutViewInterface $layoutView
     *
     * @return array
     */
    protected function getBlocks(LayoutViewInterface $layoutView)
    {
        $blocks = array();

        foreach ($layoutView->getParameters()['blocks'] as $zoneIdentifier => $zoneBlocks) {
            $blocks = array_merge($blocks, $zoneBlocks);
        }

        $normalizedBlocks = array();
        foreach ($blocks as $block) {
            $normalizedBlocks[] = $this->blockNormalizer->normalize($block);
        }

        return $normalizedBlocks;
    }

    /**
     * Returns the data for block positions.
     *
     * @param \Netgen\BlockManager\View\LayoutViewInterface $layoutView
     *
     * @return array
     */
    protected function getBlockPositions(LayoutViewInterface $layoutView)
    {
        $positions = array();

        foreach ($layoutView->getLayout()->getZones() as $zone) {
            $blocksInZone = array();
            $zoneIdentifier = $zone->getIdentifier();

            if (!empty($layoutView->getParameters()['blocks'][$zoneIdentifier])) {
                foreach ($layoutView->getParameters()['blocks'][$zoneIdentifier] as $block) {
                    /** @var \Netgen\BlockManager\API\Values\Page\Block $block */
                    $blocksInZone[] = array('block_id' => $block->getId());
                }
            }

            $positions[] = array(
                'zone' => $zoneIdentifier,
                'blocks' => $blocksInZone,
            );
        }

        return $positions;
    }
}
