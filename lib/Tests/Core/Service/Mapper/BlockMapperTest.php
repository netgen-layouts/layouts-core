<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Values\Page\CollectionReference as APICollectionReference;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference;

abstract class BlockMapperTest extends MapperTest
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->blockMapper = $this->createBlockMapper();
    }

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

        $block = $this->blockMapper->mapBlock($persistenceBlock);

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

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapCollectionReference
     */
    public function testMapCollectionReference()
    {
        $persistenceReference = new CollectionReference(
            array(
                'blockId' => 1,
                'status' => APILayout::STATUS_PUBLISHED,
                'collectionId' => 42,
                'identifier' => 'default',
                'offset' => 5,
                'limit' => 10,
            )
        );

        $reference = $this->blockMapper->mapCollectionReference($persistenceReference);

        self::assertInstanceOf(APICollectionReference::class, $reference);

        self::assertEquals(1, $reference->getBlockId());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $reference->getStatus());
        self::assertEquals(42, $reference->getCollectionId());
        self::assertEquals('default', $reference->getIdentifier());
        self::assertEquals(5, $reference->getOffset());
        self::assertEquals(10, $reference->getLimit());
    }
}
