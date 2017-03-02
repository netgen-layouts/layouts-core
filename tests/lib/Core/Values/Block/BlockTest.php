<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isPublished
     */
    public function testSetDefaultProperties()
    {
        $block = new Block();

        $this->assertNull($block->getId());
        $this->assertNull($block->getDefinition());
        $this->assertEquals(array(), $block->getParameters());
        $this->assertFalse($block->hasParameter('test'));
        $this->assertEquals(array(), $block->getPlaceholders());
        $this->assertFalse($block->hasPlaceholder('test'));
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

        try {
            $block->getPlaceholder('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isPublished
     */
    public function testSetProperties()
    {
        $block = new Block(
            array(
                'id' => 42,
                'definition' => new BlockDefinition('text'),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'status' => Value::STATUS_PUBLISHED,
                'published' => true,
                'placeholders' => array(
                    'main' => new Placeholder(array('identifier' => 'main')),
                ),
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        $this->assertEquals(42, $block->getId());
        $this->assertEquals(new BlockDefinition('text'), $block->getDefinition());
        $this->assertEquals('some_value', $block->getParameter('some_param'));
        $this->assertFalse($block->hasParameter('test'));
        $this->assertTrue($block->hasParameter('some_param'));
        $this->assertEquals(new Placeholder(array('identifier' => 'main')), $block->getPlaceholder('main'));
        $this->assertFalse($block->hasPlaceholder('test'));
        $this->assertTrue($block->hasPlaceholder('main'));
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

        $this->assertEquals(
            array(
                'main' => new Placeholder(array('identifier' => 'main')),
            ),
            $block->getPlaceholders()
        );

        try {
            $block->getPlaceholder('test');
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }
}
