<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\Core\Values\Page\Block;
use PHPUnit_Framework_TestCase;

class BlockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getZoneId
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getDefinitionIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Page\Block::getViewType
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
    }
}
