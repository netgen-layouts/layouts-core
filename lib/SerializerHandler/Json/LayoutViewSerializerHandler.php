<?php

namespace Netgen\BlockManager\SerializerHandler\Json;

use JMS\Serializer\GraphNavigator;
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

        $data = parent::getValueData($layout);

        $data['zones'] = $zones;
        $data['html'] = $this->viewRenderer->renderView($value);

        return $data;
    }
}
