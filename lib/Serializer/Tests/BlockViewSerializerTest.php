<?php

namespace Netgen\BlockManager\Serializer\Tests;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Serializer\BlockViewSerializer;
use JMS\Serializer\GraphNavigator;
use Netgen\BlockManager\View\BlockView;
use PHPUnit_Framework_TestCase;

class BlockViewSerializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\BlockViewSerializer::getSubscribingMethods
     */
    public function testGetSubscribingMethods()
    {
        $blockViewSerializer = new BlockViewSerializer(
            array(),
            $this->getMock('Netgen\BlockManager\View\Renderer\ViewRenderer')
        );

        self::assertEquals(
            array(
                array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'format' => 'json',
                    'type' => 'Netgen\BlockManager\View\BlockView',
                    'method' => 'serialize',
                ),
            ),
            $blockViewSerializer->getSubscribingMethods()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\BlockViewSerializer::__construct
     * @covers \Netgen\BlockManager\Serializer\BlockViewSerializer::getValueData
     */
    public function testGetValueData()
    {
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

        $blockView = new BlockView();
        $blockView->setBlock($block);

        $config = array(
            'paragraph' => array(
                'name' => 'Paragraph',
            ),
        );

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\Renderer\ViewRenderer');
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($blockView))
            ->will($this->returnValue('rendered block view'));

        $blockViewSerializer = new BlockViewSerializer($config, $viewRendererMock);

        self::assertEquals(
            array(
                'id' => $block->getId(),
                'definition_identifier' => $block->getDefinitionIdentifier(),
                'title' => $config[$block->getDefinitionIdentifier()]['name'],
                'zone_id' => $block->getZoneId(),
                'parameters' => $block->getParameters(),
                'view_type' => $block->getViewType(),
                'html' => 'rendered block view',
            ),
            $blockViewSerializer->getValueData($blockView)
        );
    }
}
