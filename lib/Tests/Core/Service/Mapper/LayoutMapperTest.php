<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use DateTime;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class LayoutMapperTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->layoutMapper = $this->createLayoutMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZone()
    {
        $persistenceZone = new Zone(
            array(
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => APILayout::STATUS_PUBLISHED,
                'linkedLayoutId' => 3,
                'linkedZoneIdentifier' => 'right',
            )
        );

        $zone = $this->layoutMapper->mapZone($persistenceZone);

        $this->assertInstanceOf(APIZone::class, $zone);
        $this->assertEquals('right', $zone->getIdentifier());
        $this->assertEquals(1, $zone->getLayoutId());
        $this->assertEquals(APILayout::STATUS_PUBLISHED, $zone->getStatus());
        $this->assertInstanceOf(APIZone::class, $zone->getLinkedZone());
        $this->assertEquals(APILayout::STATUS_PUBLISHED, $zone->getLinkedZone()->getStatus());
        $this->assertEquals(3, $zone->getLinkedZone()->getLayoutId());
        $this->assertEquals('right', $zone->getLinkedZone()->getIdentifier());

        $this->assertNotEmpty($zone->getBlocks());

        $position = 0;
        foreach ($zone->getBlocks() as $block) {
            $this->assertEquals($position, $block->getPosition());
            $this->assertInstanceOf(APIBlock::class, $block);
            ++$position;
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     * @covers \Netgen\BlockManager\Core\Service\Mapper\Mapper::createDateTime
     */
    public function testMapLayout()
    {
        $persistenceLayout = new Layout(
            array(
                'id' => 1,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'created' => 1447065813,
                'modified' => 1447065813,
                'status' => APILayout::STATUS_PUBLISHED,
                'shared' => true,
            )
        );

        $layout = $this->layoutMapper->mapLayout($persistenceLayout);

        $this->assertEquals(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            $layout->getLayoutType()
        );

        $this->assertInstanceOf(APILayout::class, $layout);
        $this->assertEquals(1, $layout->getId());
        $this->assertEquals('My layout', $layout->getName());
        $this->assertInstanceOf(DateTime::class, $layout->getCreated());
        $this->assertEquals(1447065813, $layout->getCreated()->getTimestamp());
        $this->assertInstanceOf(DateTime::class, $layout->getModified());
        $this->assertEquals(1447065813, $layout->getModified()->getTimestamp());
        $this->assertEquals(APILayout::STATUS_PUBLISHED, $layout->getStatus());
        $this->assertTrue($layout->isShared());

        $this->assertNotEmpty($layout->getZones());

        foreach ($layout->getZones() as $zone) {
            $this->assertInstanceOf(APIZone::class, $zone);
        }
    }
}
