<?php

namespace Netgen\BlockManager\Serializer;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;

class BlockSerializer extends Serializer implements SubscribingHandlerInterface
{
    /**
     * @var array
     */
    protected $blockConfig;

    /**
     * Constructor.
     *
     * @param array $blockConfig
     */
    public function __construct(array $blockConfig)
    {
        $this->blockConfig = $blockConfig;
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
                'type' => 'Netgen\BlockManager\Core\Values\Page\Block',
                'method' => 'serialize',
            ),
        );
    }

    /**
     * Returns the data that will be serialized.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $value
     *
     * @return array
     */
    public function getValueData($value)
    {
        $blockDefinitionIdentifier = $value->getDefinitionIdentifier();

        return array(
            'id' => $value->getId(),
            'definition_identifier' => $blockDefinitionIdentifier,
            'title' => $this->blockConfig[$blockDefinitionIdentifier]['name'],
            'zone_id' => $value->getZoneId(),
            'parameters' => $value->getParameters(),
            'view_type' => $value->getViewType(),
        );
    }
}
