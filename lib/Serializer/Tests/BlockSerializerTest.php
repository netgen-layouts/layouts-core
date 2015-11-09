<?php

namespace Netgen\BlockManager\Serializer\Tests;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\BlockSerializer;
use JMS\Serializer\GraphNavigator;
use PHPUnit_Framework_TestCase;

class BlockSerializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\BlockSerializer::getSubscribingMethods
     */
    public function testGetSubscribingMethods()
    {
        $blockSerializer = new BlockSerializer(array());

        self::assertEquals(
            array(
                array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'format' => 'json',
                    'type' => 'Netgen\BlockManager\Core\Values\Page\Block',
                    'method' => 'serialize',
                ),
            ),
            $blockSerializer->getSubscribingMethods()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\BlockSerializer::__construct
     * @covers \Netgen\BlockManager\Serializer\BlockSerializer::getValueData
     */
    public function testGetValueData()
    {
        $config = array(
            'paragraph' => array(
                'name' => 'Paragraph',
            ),
        );

        $blockSerializer = new BlockSerializer($config);

        $block = new Block(
            array(
                'id' => 42,
                'zoneId' => 24,
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
            )
        );

        self::assertEquals(
            array(
                'id' => $block->getId(),
                'definition_identifier' => $block->getDefinitionIdentifier(),
                'title' => $config[$block->getDefinitionIdentifier()]['name'],
                'zone_id' => $block->getZoneId(),
                'parameters' => $block->getParameters(),
                'view_type' => $block->getViewType(),
            ),
            $blockSerializer->getValueData($block)
        );
    }
}
