<?php

namespace Netgen\BlockManager\Serializer\Tests;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Serializer\LayoutSerializer;
use JMS\Serializer\GraphNavigator;
use PHPUnit_Framework_TestCase;
use DateTime;

class LayoutSerializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\LayoutSerializer::getSubscribingMethods
     */
    public function testGetSubscribingMethods()
    {
        $layoutSerializer = new LayoutSerializer(array());

        self::assertEquals(
            array(
                array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'format' => 'json',
                    'type' => 'Netgen\BlockManager\Core\Values\Page\Layout',
                    'method' => 'serialize',
                ),
            ),
            $layoutSerializer->getSubscribingMethods()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\LayoutSerializer::__construct
     * @covers \Netgen\BlockManager\Serializer\LayoutSerializer::getValueData
     */
    public function testGetValueData()
    {
        $config = array(
            '3_zones_a' => array(
                'name' => '3 zones A',
            ),
        );

        $layoutSerializer = new LayoutSerializer($config);

        $currentDate = new DateTime();
        $currentDate->setTimestamp(time());

        $layout = new Layout(
            array(
                'id' => 42,
                'parentId' => 24,
                'identifier' => '3_zones_a',
                'created' => $currentDate,
                'modified' => $currentDate,
            )
        );

        self::assertEquals(
            array(
                'id' => $layout->getId(),
                'parent_id' => $layout->getParentId(),
                'identifier' => $layout->getIdentifier(),
                'created_at' => $layout->getCreated(),
                'updated_at' => $layout->getModified(),
                'title' => $config[$layout->getIdentifier()]['name'],
            ),
            $layoutSerializer->getValueData($layout)
        );
    }
}
