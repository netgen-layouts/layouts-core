<?php

namespace Netgen\BlockManager\Serializer\Tests;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Serializer\LayoutViewSerializer;
use JMS\Serializer\GraphNavigator;
use Netgen\BlockManager\View\LayoutView;
use PHPUnit_Framework_TestCase;
use DateTime;

class LayoutViewSerializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\LayoutViewSerializer::getSubscribingMethods
     */
    public function testGetSubscribingMethods()
    {
        $layoutViewSerializer = new LayoutViewSerializer(
            array(),
            $this->getMock('Netgen\BlockManager\View\Renderer\ViewRenderer')
        );

        self::assertEquals(
            array(
                array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'format' => 'json',
                    'type' => 'Netgen\BlockManager\View\LayoutView',
                    'method' => 'serialize',
                ),
            ),
            $layoutViewSerializer->getSubscribingMethods()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\LayoutViewSerializer::__construct
     * @covers \Netgen\BlockManager\Serializer\LayoutViewSerializer::getValueData
     * @covers \Netgen\BlockManager\Serializer\LayoutViewSerializer::getZones
     * @covers \Netgen\BlockManager\Serializer\LayoutViewSerializer::getBlocks
     * @covers \Netgen\BlockManager\Serializer\LayoutViewSerializer::getBlockPositions
     */
    public function testGetValueData()
    {
        $currentDate = new DateTime();
        $currentDate->setTimestamp(time());

        $layout = new Layout(
            array(
                'id' => 42,
                'parentId' => null,
                'identifier' => '3_zones_a',
                'created' => $currentDate,
                'modified' => $currentDate,
                'zones' => array(
                    new Zone(
                        array(
                            'id' => 84,
                            'layoutId' => 42,
                            'identifier' => 'left',
                        )
                    ),
                    new Zone(
                        array(
                            'id' => 85,
                            'layoutId' => 42,
                            'identifier' => 'right',
                        )
                    ),
                ),
            )
        );

        $block = new Block(
            array(
                'id' => 24,
                'zoneId' => 84,
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
            )
        );

        $layoutView = new LayoutView();
        $layoutView->setLayout($layout);
        $layoutView->addParameters(
            array(
                'blocks' => array(
                    'left' => array($block),
                ),
            )
        );

        $config = array(
            '3_zones_a' => array(
                'name' => '3 zones A',
                'zones' => array(
                    'left' => array(),
                    'right' => array(
                        'allowed_blocks' => array('paragraph'),
                    ),
                ),
            ),
        );

        $viewRendererMock = $this->getMock('Netgen\BlockManager\View\Renderer\ViewRenderer');
        $viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($layoutView))
            ->will($this->returnValue('rendered layout view'));

        $layoutViewSerializer = new LayoutViewSerializer($config, $viewRendererMock);

        self::assertEquals(
            array(
                'id' => $layout->getId(),
                'parent_id' => $layout->getParentId(),
                'identifier' => $layout->getIdentifier(),
                'created_at' => $layout->getCreated(),
                'updated_at' => $layout->getModified(),
                'title' => $config[$layout->getIdentifier()]['name'],
                'html' => 'rendered layout view',
                'zones' => array(
                    array(
                        'identifier' => 'left',
                        'accepts' => true,
                    ),
                    array(
                        'identifier' => 'right',
                        'accepts' => array('paragraph'),
                    ),
                ),
                'blocks' => array($block),
                'positions' => array(
                    array(
                        'zone' => 'left',
                        'blocks' => array(
                            array(
                                'block_id' => $block->getId(),
                            ),
                        ),
                    ),
                    array(
                        'zone' => 'right',
                        'blocks' => array(),
                    ),
                ),
            ),
            $layoutViewSerializer->getValueData($layoutView)
        );
    }
}
