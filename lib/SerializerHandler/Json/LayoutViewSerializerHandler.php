<?php

namespace Netgen\BlockManager\SerializerHandler\Json;

use JMS\Serializer\GraphNavigator;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutViewInterface;
use Netgen\BlockManager\View\Renderer\ViewRenderer;

class LayoutViewSerializerHandler extends LayoutSerializerHandler
{
    /**
     * @var \Netgen\BlockManager\View\Renderer\ViewRenderer
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Renderer\ViewRenderer $viewRenderer
     * @param array $layoutConfig
     */
    public function __construct(array $layoutConfig, ViewRenderer $viewRenderer)
    {
        parent::__construct($layoutConfig);

        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Returns the serializer handler definition array.
     *
     * The direction and method keys can be omitted.
     *
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'Netgen\BlockManager\View\LayoutView',
                'method' => 'serialize',
            ),
        );
    }

    /**
     * Returns the data that will be serialized.
     *
     * @param \Netgen\BlockManager\View\LayoutViewInterface $value
     *
     * @return array
     */
    public function getValueData($value)
    {
        $layout = $value->getLayout();

        $data = parent::getValueData($layout);

        $data['zones'] = $this->getZones($layout);
        $data['blocks'] = $this->getBlocks($value);
        $data['positions'] = $this->getBlockPositions($value);
        $data['html'] = $this->viewRenderer->renderView($value);

        return $data;
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

        return $blocks;
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

        foreach ($layoutView->getParameters()['blocks'] as $zoneIdentifier => $blocks) {
            $blocksInZone = array();
            foreach ($blocks as $block) {
                /** @var \Netgen\BlockManager\API\Values\Page\Block $block */
                $blocksInZone[] = array('block_id' => $block->getId());
            }

            $positions[] = array(
                'zone' => $zoneIdentifier,
                'blocks' => $blocksInZone,
            );
        }

        return $positions;
    }
}
