<?php

namespace Netgen\BlockManager\Tests\View\View\BlockView;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Block\Block as CoreBlock;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\View\View\BlockView\Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::__construct
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getId
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getDefinition
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getParameters
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getViewType
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getItemViewType
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getName
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getStatus
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::isPublished
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getPlaceholders
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getPlaceholder
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasDynamicParameter
     */
    public function testSetDefaultProperties()
    {
        $block = new Block(new CoreBlock());

        $this->assertNull($block->getId());
        $this->assertNull($block->getDefinition());
        $this->assertEquals(array(), $block->getParameters());
        $this->assertFalse($block->hasParameter('test'));
        $this->assertNull($block->getViewType());
        $this->assertNull($block->getItemViewType());
        $this->assertNull($block->getName());
        $this->assertNull($block->getStatus());
        $this->assertNull($block->isPublished());
        $this->assertFalse($block->hasPlaceholder('test'));
        $this->assertEquals(array(), $block->getPlaceholders());
        $this->assertNull($block->getDynamicParameter('dynamic'));
        $this->assertFalse($block->hasDynamicParameter('dynamic'));

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
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::__construct
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getId
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getDefinition
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getParameters
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getViewType
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getItemViewType
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getName
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getStatus
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::isPublished
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getPlaceholders
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getPlaceholder
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\View\View\BlockView\Block::hasDynamicParameter
     */
    public function testSetProperties()
    {
        $block = new CoreBlock(
            array(
                'id' => 42,
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
                'placeholders' => array(
                    'main' => new Placeholder(),
                ),
            )
        );

        $block = new Block($block, array('dynamic' => 'value'));

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
        $this->assertEquals(new Placeholder(), $block->getPlaceholder('main'));
        $this->assertFalse($block->hasPlaceholder('test'));
        $this->assertTrue($block->hasPlaceholder('main'));
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

        $this->assertEquals(
            array(
                'main' => new Placeholder(),
            ),
            $block->getPlaceholders()
        );

        try {
            $block->getPlaceholder('test');
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
