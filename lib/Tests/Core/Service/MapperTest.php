<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use DateTime;

abstract class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates the mapper under test.
     *
     * @return \Netgen\BlockManager\API\Service\Mapper
     */
    abstract protected function createMapper();

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper::__construct
     */
    public function testMapBlock()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 1,
                'zoneId' => 2,
                'definitionIdentifier' => 'paragraph',
                'parameters' => array(
                    'some_param' => 'some_value',
                ),
                'viewType' => 'default',
                'name' => 'My block',
                'status' => APILayout::STATUS_PUBLISHED,
            )
        );

        $mapper = $this->createMapper();
        $block = $mapper->mapBlock($persistenceBlock);

        self::assertInstanceOf(APIBlock::class, $block);
        self::assertEquals(1, $block->getId());
        self::assertEquals(2, $block->getZoneId());
        self::assertEquals('paragraph', $block->getDefinitionIdentifier());
        self::assertEquals(array('some_param' => 'some_value'), $block->getParameters());
        self::assertEquals('default', $block->getViewType());
        self::assertEquals('My block', $block->getName());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $block->getStatus());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper::mapZone
     */
    public function testMapZone()
    {
        $persistenceZone = new Zone(
            array(
                'id' => 2,
                'layoutId' => 1,
                'identifier' => 'top_right',
                'status' => APILayout::STATUS_PUBLISHED,
            )
        );

        $mapper = $this->createMapper();
        $zone = $mapper->mapZone($persistenceZone);

        self::assertInstanceOf(APIZone::class, $zone);
        self::assertEquals(2, $zone->getId());
        self::assertEquals(1, $zone->getLayoutId());
        self::assertEquals('top_right', $zone->getIdentifier());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $zone->getStatus());

        self::assertNotEmpty($zone->getBlocks());

        foreach ($zone->getBlocks() as $block) {
            self::assertInstanceOf(APIBlock::class, $block);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper::mapLayout
     * @covers \Netgen\BlockManager\Core\Service\Mapper::createDateTime
     */
    public function testMapLayout()
    {
        $persistenceLayout = new Layout(
            array(
                'id' => 1,
                'parentId' => null,
                'identifier' => '3_zones_a',
                'name' => 'My layout',
                'created' => 1447065813,
                'modified' => 1447065813,
                'status' => APILayout::STATUS_PUBLISHED,
            )
        );

        $mapper = $this->createMapper();
        $layout = $mapper->mapLayout($persistenceLayout);

        self::assertInstanceOf(APILayout::class, $layout);
        self::assertEquals(1, $layout->getId());
        self::assertEquals(null, $layout->getParentId());
        self::assertEquals('3_zones_a', $layout->getIdentifier());
        self::assertEquals('My layout', $layout->getName());
        self::assertInstanceOf(DateTime::class, $layout->getCreated());
        self::assertEquals(1447065813, $layout->getCreated()->getTimestamp());
        self::assertInstanceOf(DateTime::class, $layout->getModified());
        self::assertEquals(1447065813, $layout->getModified()->getTimestamp());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $layout->getStatus());

        self::assertNotEmpty($layout->getZones());

        foreach ($layout->getZones() as $zone) {
            self::assertInstanceOf(APIZone::class, $zone);
        }
    }
}
