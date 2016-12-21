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
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::isPublished
     */
    public function testSetDefaultProperties()
    {
        $block = new Block();

        $this->assertNull($block->getId());
        $this->assertNull($block->getDefinition());
        $this->assertEquals(array(), $block->getParameters());
        $this->assertFalse($block->hasParameter('test'));
        $this->assertNull($block->getViewType());
        $this->assertNull($block->getItemViewType());
        $this->assertNull($block->getName());
        $this->assertNull($block->getStatus());
        $this->assertNull($block->isPublished());

        try {
            $block->getParameter('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::isPublished
     */
    public function testSetProperties()
    {
        $block = new Block(
            array(
                'id' => 42,
                'layoutId' => 84,
                'zoneIdentifier' => 'left',
                'position' => 3,
                'definition' => new BlockDefinition('text'),
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

        $this->assertEquals(42, $block->getId());
        $this->assertEquals(new BlockDefinition('text'), $block->getDefinition());
        $this->assertEquals('some_value', $block->getParameter('some_param'));
        $this->assertFalse($block->hasParameter('test'));
        $this->assertTrue($block->hasParameter('some_param'));
        $this->assertEquals('default', $block->getViewType());
        $this->assertEquals('standard', $block->getItemViewType());
        $this->assertEquals('My block', $block->getName());
        $this->assertTrue($block->isPublished());
        $this->assertTrue($block->isPublished());

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
