<?php

namespace Netgen\BlockManager\Serializer;

use JMS\Serializer\GraphNavigator;
use Netgen\BlockManager\View\Renderer\ViewRenderer;

class BlockViewSerializer extends BlockSerializer
{
    /**
     * @var \Netgen\BlockManager\View\Renderer\ViewRenderer
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Renderer\ViewRenderer $viewRenderer
     * @param array $blockConfig
     */
    public function __construct(array $blockConfig, ViewRenderer $viewRenderer)
    {
        parent::__construct($blockConfig);

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
                'type' => 'Netgen\BlockManager\View\BlockView',
                'method' => 'serialize',
            ),
        );
    }

    /**
     * Returns the data that will be serialized.
     *
     * @param \Netgen\BlockManager\View\BlockViewInterface $value
     *
     * @return array
     */
    public function getValueData($value)
    {
        $data = parent::getValueData($value->getBlock());
        $data['html'] = $this->viewRenderer->renderView($value);

        return $data;
    }
}
