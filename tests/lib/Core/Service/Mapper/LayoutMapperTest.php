<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use DateTimeInterface;
use Netgen\BlockManager\API\Values\Layout\Layout as APILayout;
use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Layout\Type\NullLayoutType;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class LayoutMapperTest extends ServiceTestCase
{
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
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutId' => 3,
                'linkedZoneIdentifier' => 'right',
            ]
        );

        $zone = $this->layoutMapper->mapZone($persistenceZone);

        $this->assertInstanceOf(APIZone::class, $zone);
        $this->assertEquals('right', $zone->getIdentifier());
        $this->assertEquals(1, $zone->getLayoutId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $zone->getStatus());
        $this->assertInstanceOf(APIZone::class, $zone->getLinkedZone());
        $this->assertTrue($zone->getLinkedZone()->isPublished());
        $this->assertEquals(3, $zone->getLinkedZone()->getLayoutId());
        $this->assertEquals('right', $zone->getLinkedZone()->getIdentifier());
        $this->assertTrue($zone->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZoneWithNoLinkedZone()
    {
        $persistenceZone = new Zone(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutId' => null,
                'linkedZoneIdentifier' => null,
            ]
        );

        $zone = $this->layoutMapper->mapZone($persistenceZone);

        $this->assertInstanceOf(APIZone::class, $zone);
        $this->assertEquals('right', $zone->getIdentifier());
        $this->assertEquals(1, $zone->getLayoutId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $zone->getStatus());
        $this->assertNull($zone->getLinkedZone());
        $this->assertTrue($zone->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZoneWithNonExistingLinkedZone()
    {
        $persistenceZone = new Zone(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutId' => 9999,
                'linkedZoneIdentifier' => 'unknown',
            ]
        );

        $zone = $this->layoutMapper->mapZone($persistenceZone);

        $this->assertInstanceOf(APIZone::class, $zone);
        $this->assertEquals('right', $zone->getIdentifier());
        $this->assertEquals(1, $zone->getLayoutId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $zone->getStatus());
        $this->assertNull($zone->getLinkedZone());
        $this->assertTrue($zone->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     */
    public function testMapLayout()
    {
        $persistenceLayout = new Layout(
            [
                'id' => 1,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My description',
                'created' => 1447065813,
                'modified' => 1447065813,
                'status' => Value::STATUS_PUBLISHED,
                'shared' => true,
            ]
        );

        $layout = $this->layoutMapper->mapLayout($persistenceLayout);

        $this->assertEquals(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            $layout->getLayoutType()
        );

        $this->assertInstanceOf(APILayout::class, $layout);
        $this->assertEquals(1, $layout->getId());
        $this->assertEquals('My layout', $layout->getName());
        $this->assertEquals('My description', $layout->getDescription());
        $this->assertInstanceOf(DateTimeInterface::class, $layout->getCreated());
        $this->assertEquals(1447065813, $layout->getCreated()->getTimestamp());
        $this->assertInstanceOf(DateTimeInterface::class, $layout->getModified());
        $this->assertEquals(1447065813, $layout->getModified()->getTimestamp());
        $this->assertEquals(Value::STATUS_PUBLISHED, $layout->getStatus());
        $this->assertTrue($layout->isShared());
        $this->assertTrue($layout->isPublished());

        $this->assertNotEmpty($layout->getZones());

        foreach ($layout as $zone) {
            $this->assertInstanceOf(APIZone::class, $zone);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     */
    public function testMapLayoutWithInvalidLayoutType()
    {
        $persistenceLayout = new Layout(
            [
                'id' => 1,
                'type' => 'unknown',
                'name' => 'My layout',
                'description' => 'My description',
                'created' => 1447065813,
                'modified' => 1447065813,
                'status' => Value::STATUS_PUBLISHED,
                'shared' => true,
            ]
        );

        $layout = $this->layoutMapper->mapLayout($persistenceLayout);

        $this->assertInstanceOf(NullLayoutType::class, $layout->getLayoutType());

        $this->assertInstanceOf(APILayout::class, $layout);
        $this->assertEquals(1, $layout->getId());
        $this->assertEquals('My layout', $layout->getName());
        $this->assertEquals('My description', $layout->getDescription());
        $this->assertInstanceOf(DateTimeInterface::class, $layout->getCreated());
        $this->assertEquals(1447065813, $layout->getCreated()->getTimestamp());
        $this->assertInstanceOf(DateTimeInterface::class, $layout->getModified());
        $this->assertEquals(1447065813, $layout->getModified()->getTimestamp());
        $this->assertEquals(Value::STATUS_PUBLISHED, $layout->getStatus());
        $this->assertTrue($layout->isShared());
        $this->assertTrue($layout->isPublished());

        $this->assertNotEmpty($layout->getZones());

        foreach ($layout as $zone) {
            $this->assertInstanceOf(APIZone::class, $zone);
        }
    }
}
