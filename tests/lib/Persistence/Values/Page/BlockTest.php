<?php

namespace Netgen\BlockManager\Tests\Persistence\Values;

use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $block = new Block();

        $this->assertNull($block->id);
        $this->assertNull($block->layoutId);
        $this->assertNull($block->zoneIdentifier);
        $this->assertNull($block->position);
        $this->assertNull($block->definitionIdentifier);
        $this->assertNull($block->parameters);
        $this->assertNull($block->viewType);
        $this->assertNull($block->itemViewType);
        $this->assertNull($block->name);
        $this->assertNull($block->status);
    }

    public function testSetProperties()
    {
        $block = new Block(
            array(
                'id' => 42,
                'layoutId' => 84,
                'zoneIdentifier' => 'top',
                'position' => 4,
                'definitionIdentifier' => 'text',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'status' => Value::STATUS_DRAFT,
            )
        );

        $this->assertEquals(42, $block->id);
        $this->assertEquals(84, $block->layoutId);
        $this->assertEquals('top', $block->zoneIdentifier);
        $this->assertEquals(4, $block->position);
        $this->assertEquals('text', $block->definitionIdentifier);
        $this->assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $block->parameters
        );
        $this->assertEquals('default', $block->viewType);
        $this->assertEquals('standard', $block->itemViewType);
        $this->assertEquals('My block', $block->name);
        $this->assertEquals(Value::STATUS_DRAFT, $block->status);
    }
}
