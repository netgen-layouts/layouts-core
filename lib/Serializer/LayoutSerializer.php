<?php

namespace Netgen\BlockManager\Serializer;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;

class LayoutSerializer extends Serializer implements SubscribingHandlerInterface
{
    /**
     * @var array
     */
    protected $layoutConfig;

    /**
     * Constructor.
     *
     * @param array $layoutConfig
     */
    public function __construct(array $layoutConfig)
    {
        $this->layoutConfig = $layoutConfig;
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
                'type' => 'Netgen\BlockManager\Core\Values\Page\Layout',
                'method' => 'serialize',
            ),
        );
    }

    /**
     * Returns the data that will be serialized.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $value
     *
     * @return array
     */
    public function getValueData($value)
    {
        return array(
            'id' => $value->getId(),
            'parent_id' => $value->getParentId(),
            'identifier' => $value->getIdentifier(),
            'created_at' => $value->getCreated(),
            'updated_at' => $value->getModified(),
            'title' => $this->layoutConfig[$value->getIdentifier()]['name'],
        );
    }
}
