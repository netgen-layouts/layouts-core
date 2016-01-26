<?php

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;

class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getZoneId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getDefinitionIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getStatus
     */
    public function testSetDefaultProperties()
    {
        $block = new Block();

        self::assertNull($block->getId());
        self::assertNull($block->getZoneId());
        self::assertNull($block->getDefinitionIdentifier());
        self::assertEquals(array(), $block->getParameters());
        self::assertNull($block->getViewType());
        self::assertNull($block->getName());
        self::assertNull($block->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getZoneId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getDefinitionIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getStatus
     */
    public function testSetProperties()
    {
        $block = new Block(
            array(
                'id' => 42,
                'zoneId' => 84,
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
                'name' => 'My block',
                'status' => Layout::STATUS_PUBLISHED,
            )
        );

        self::assertEquals(42, $block->getId());
        self::assertEquals(84, $block->getZoneId());
        self::assertEquals('paragraph', $block->getDefinitionIdentifier());
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $block->getParameters()
        );
        self::assertEquals('default', $block->getViewType());
        self::assertEquals('My block', $block->getName());
        self::assertEquals(Layout::STATUS_PUBLISHED, $block->getStatus());
    }
}
