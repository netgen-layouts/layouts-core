<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getZoneIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getBlockDefinition
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

        $this->assertNull($block->getId());
        $this->assertNull($block->getLayoutId());
        $this->assertNull($block->getZoneIdentifier());
        $this->assertNull($block->getPosition());
        $this->assertNull($block->getBlockDefinition());
        $this->assertEquals(array(), $block->getParameters());
        $this->assertFalse($block->hasParameter('test'));
        $this->assertNull($block->getViewType());
        $this->assertNull($block->getItemViewType());
        $this->assertNull($block->getName());
        $this->assertNull($block->getStatus());

        try {
            $block->getParameter('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getZoneIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getPosition
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getBlockDefinition
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
                'blockDefinition' => new BlockDefinition('text'),
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $this->assertEquals(42, $block->getId());
        $this->assertEquals(84, $block->getLayoutId());
        $this->assertEquals('left', $block->getZoneIdentifier());
        $this->assertEquals(3, $block->getPosition());
        $this->assertEquals(new BlockDefinition('text'), $block->getBlockDefinition());
        $this->assertEquals('some_value', $block->getParameter('some_param'));
        $this->assertFalse($block->hasParameter('test'));
        $this->assertTrue($block->hasParameter('some_param'));
        $this->assertEquals('default', $block->getViewType());
        $this->assertEquals('standard', $block->getItemViewType());
        $this->assertEquals('My block', $block->getName());
        $this->assertEquals(Value::STATUS_PUBLISHED, $block->getStatus());

        $this->assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $block->getParameters()
        );

        try {
            $block->getParameter('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }
}
