<?php

declare(strict_types=1);

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
    public function setUp(): void
    {
        parent::setUp();

        $this->layoutMapper = $this->createLayoutMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZone(): void
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
        $this->assertSame('right', $zone->getIdentifier());
        $this->assertSame(1, $zone->getLayoutId());
        $this->assertTrue($zone->isPublished());
        $this->assertInstanceOf(APIZone::class, $zone->getLinkedZone());
        $this->assertTrue($zone->getLinkedZone()->isPublished());
        $this->assertSame(3, $zone->getLinkedZone()->getLayoutId());
        $this->assertSame('right', $zone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZoneWithNoLinkedZone(): void
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
        $this->assertSame('right', $zone->getIdentifier());
        $this->assertSame(1, $zone->getLayoutId());
        $this->assertTrue($zone->isPublished());
        $this->assertNull($zone->getLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZoneWithNonExistingLinkedZone(): void
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
        $this->assertSame('right', $zone->getIdentifier());
        $this->assertSame(1, $zone->getLayoutId());
        $this->assertTrue($zone->isPublished());
        $this->assertNull($zone->getLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     */
    public function testMapLayout(): void
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

        $this->assertSame(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            $layout->getLayoutType()
        );

        $this->assertInstanceOf(APILayout::class, $layout);
        $this->assertSame(1, $layout->getId());
        $this->assertSame('My layout', $layout->getName());
        $this->assertSame('My description', $layout->getDescription());
        $this->assertInstanceOf(DateTimeInterface::class, $layout->getCreated());
        $this->assertSame(1447065813, $layout->getCreated()->getTimestamp());
        $this->assertInstanceOf(DateTimeInterface::class, $layout->getModified());
        $this->assertSame(1447065813, $layout->getModified()->getTimestamp());
        $this->assertTrue($layout->isPublished());
        $this->assertTrue($layout->isShared());

        $this->assertNotEmpty($layout->getZones());

        foreach ($layout as $zone) {
            $this->assertInstanceOf(APIZone::class, $zone);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     */
    public function testMapLayoutWithInvalidLayoutType(): void
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
        $this->assertSame(1, $layout->getId());
        $this->assertSame('My layout', $layout->getName());
        $this->assertSame('My description', $layout->getDescription());
        $this->assertInstanceOf(DateTimeInterface::class, $layout->getCreated());
        $this->assertSame(1447065813, $layout->getCreated()->getTimestamp());
        $this->assertInstanceOf(DateTimeInterface::class, $layout->getModified());
        $this->assertSame(1447065813, $layout->getModified()->getTimestamp());
        $this->assertTrue($layout->isPublished());
        $this->assertTrue($layout->isShared());

        $this->assertNotEmpty($layout->getZones());

        foreach ($layout as $zone) {
            $this->assertInstanceOf(APIZone::class, $zone);
        }
    }
}
