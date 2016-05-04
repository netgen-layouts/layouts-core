<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use DateTime;

abstract class LayoutMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates the mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    abstract protected function createLayoutMapper();

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZone()
    {
        $persistenceZone = new Zone(
            array(
                'identifier' => 'top_right',
                'layoutId' => 1,
                'status' => APILayout::STATUS_PUBLISHED,
            )
        );

        $mapper = $this->createLayoutMapper();
        $zone = $mapper->mapZone($persistenceZone);

        self::assertInstanceOf(APIZone::class, $zone);
        self::assertEquals('top_right', $zone->getIdentifier());
        self::assertEquals(1, $zone->getLayoutId());
        self::assertEquals(APILayout::STATUS_PUBLISHED, $zone->getStatus());

        self::assertNotEmpty($zone->getBlocks());

        $position = 0;
        foreach ($zone->getBlocks() as $block) {
            self::assertEquals($position, $block->getPosition());
            self::assertInstanceOf(APIBlock::class, $block);
            ++$position;
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::createDateTime
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

        $mapper = $this->createLayoutMapper();
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
