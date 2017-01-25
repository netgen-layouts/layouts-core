<?php

namespace Netgen\BlockManager\Tests\View\View\BlockView;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Page\Block as CoreBlock;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\View\View\BlockView\Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::__construct
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getId
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getLayoutId
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getZoneIdentifier
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getPosition
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getBlockDefinition
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getParameters
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getViewType
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getItemViewType
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getName
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getStatus
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::isPublished
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasDynamicParameter
     */
    public function testSetDefaultProperties()
    {
        $block = new Block(new CoreBlock());

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
        $this->assertNull($block->isPublished());
        $this->assertNull($block->getDynamicParameter('dynamic'));
        $this->assertFalse($block->hasDynamicParameter('dynamic'));

        try {
            $block->getParameter('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::__construct
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getId
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getLayoutId
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getZoneIdentifier
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getPosition
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getBlockDefinition
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getParameters
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getViewType
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getItemViewType
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getName
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getStatus
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::isPublished
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasDynamicParameter
     */
    public function testSetProperties()
    {
        $block = new CoreBlock(
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
                'published' => true,
            )
        );

        $block = new Block($block, array('dynamic' => 'value'));

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
        $this->assertTrue($block->isPublished());
        $this->assertTrue($block->isPublished());
        $this->assertEquals('value', $block->getDynamicParameter('dynamic'));
        $this->assertTrue($block->hasDynamicParameter('dynamic'));
        $this->assertFalse($block->hasDynamicParameter('test'));

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

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasDynamicParameter
     */
    public function testDynamicParametersWithClosure()
    {
        $block = new Block(
            new CoreBlock(),
            array(
                'dynamic' => function () {
                    return 'value';
                },
            )
        );

        $this->assertEquals('value', $block->getDynamicParameter('dynamic'));
        $this->assertTrue($block->hasDynamicParameter('dynamic'));
        $this->assertFalse($block->hasDynamicParameter('test'));
    }
}