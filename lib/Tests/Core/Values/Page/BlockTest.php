<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getZoneIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getDefinitionIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getStatus
     */
    public function testSetDefaultProperties()
    {
        $block = new Block();

        self::assertNull($block->getId());
        self::assertNull($block->getLayoutId());
        self::assertNull($block->getZoneIdentifier());
        self::assertNull($block->getPosition());
        self::assertNull($block->getDefinitionIdentifier());
        self::assertEquals(array(), $block->getParameters());
        self::assertNull($block->getParameter('test'));
        self::assertFalse($block->hasParameter('test'));
        self::assertNull($block->getViewType());
        self::assertNull($block->getItemViewType());
        self::assertNull($block->getName());
        self::assertNull($block->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getZoneIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getDefinitionIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getStatus
     */
    public function testSetProperties()
    {
        $block = new Block(
            array(
                'id' => 42,
                'layoutId' => 84,
                'zoneIdentifier' => 'left',
                'position' => 3,
                'definitionIdentifier' => 'text',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'status' => Layout::STATUS_PUBLISHED,
            )
        );

        self::assertEquals(42, $block->getId());
        self::assertEquals(84, $block->getLayoutId());
        self::assertEquals('left', $block->getZoneIdentifier());
        self::assertEquals(3, $block->getPosition());
        self::assertEquals('text', $block->getDefinitionIdentifier());
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $block->getParameters()
        );
        self::assertNull($block->getParameter('test'));
        self::assertEquals('some_value', $block->getParameter('some_param'));
        self::assertFalse($block->hasParameter('test'));
        self::assertTrue($block->hasParameter('some_param'));
        self::assertEquals('default', $block->getViewType());
        self::assertEquals('standard', $block->getItemViewType());
        self::assertEquals('My block', $block->getName());
        self::assertEquals(Layout::STATUS_PUBLISHED, $block->getStatus());
    }
}
