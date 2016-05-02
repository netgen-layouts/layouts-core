<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\Persistence\Values\Page\Block;

abstract class BlockMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates the mapper under test.
     *
     * @return \Netgen\BlockManager\API\Service\Mapper\BlockMapper
     */
    abstract protected function createBlockMapper();

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     */
    public function testMapBlock()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 1,
                'layoutId' => 1,
                'zoneIdentifier' => 'top_right',
                'position' => 3,
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                ),
                'viewType' => 'default',
                'name' => 'My block',
                'status' => APILayout::STATUS_PUBLISHED,
            )
        );

        $mapper = $this->createBlockMapper();
        $block = $mapper->mapBlock($persistenceBlock);

        self::assertInstanceOf(APIBlock::class, $block);
        self::assertEquals(1, $block->getId());
        self::assertEquals(1, $block->getLayoutId());
        self::assertEquals('top_right', $block->getZoneIdentifier());
        self::assertEquals(3, $block->getPosition());
        self::assertEquals('paragraph', $block->getDefinitionIdentifier());
        self::assertEquals(array('some_param' => 'some_value'), $block->getParameters());
        self::assertEquals('default', $block->getViewType());
        self::assertEquals('My block', $block->getName());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $block->getStatus());
    }
}
