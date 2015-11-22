<?php

namespace Netgen\BlockManager\Persistence\Tests\Values;

use Netgen\BlockManager\Persistence\Values\Page\Block;

class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Page\Block::__construct
     */
    public function testSetDefaultProperties()
    {
        $block = new Block();

        self::assertNull($block->id);
        self::assertNull($block->zoneId);
        self::assertNull($block->definitionIdentifier);
        self::assertNull($block->parameters);
        self::assertNull($block->viewType);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Page\Block::__construct
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
            )
        );

        self::assertEquals(42, $block->id);
        self::assertEquals(84, $block->zoneId);
        self::assertEquals('paragraph', $block->definitionIdentifier);
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $block->parameters
        );
        self::assertEquals('default', $block->viewType);
    }
}
