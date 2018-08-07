<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Layout\Type\NullLayoutType;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class LayoutMapperTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    private $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createLayoutMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutId' => 3,
                'linkedZoneIdentifier' => 'right',
            ]
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->getIdentifier());
        self::assertSame(1, $zone->getLayoutId());
        self::assertTrue($zone->isPublished());
        self::assertInstanceOf(APIZone::class, $zone->getLinkedZone());
        self::assertTrue($zone->getLinkedZone()->isPublished());
        self::assertSame(3, $zone->getLinkedZone()->getLayoutId());
        self::assertSame('right', $zone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZoneWithNoLinkedZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutId' => null,
                'linkedZoneIdentifier' => null,
            ]
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->getIdentifier());
        self::assertSame(1, $zone->getLayoutId());
        self::assertTrue($zone->isPublished());
        self::assertNull($zone->getLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapZone
     */
    public function testMapZoneWithNonExistingLinkedZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutId' => 9999,
                'linkedZoneIdentifier' => 'unknown',
            ]
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->getIdentifier());
        self::assertSame(1, $zone->getLayoutId());
        self::assertTrue($zone->isPublished());
        self::assertNull($zone->getLinkedZone());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     */
    public function testMapLayout(): void
    {
        $persistenceLayout = Layout::fromArray(
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

        $layout = $this->mapper->mapLayout($persistenceLayout);

        self::assertSame(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            $layout->getLayoutType()
        );

        self::assertSame(1, $layout->getId());
        self::assertSame('My layout', $layout->getName());
        self::assertSame('My description', $layout->getDescription());
        self::assertSame(1447065813, $layout->getCreated()->getTimestamp());
        self::assertSame(1447065813, $layout->getModified()->getTimestamp());
        self::assertTrue($layout->isPublished());
        self::assertTrue($layout->isShared());

        self::assertNotEmpty($layout->getZones());
        self::assertContainsOnlyInstancesOf(APIZone::class, $layout->getZones());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper::mapLayout
     */
    public function testMapLayoutWithInvalidLayoutType(): void
    {
        $persistenceLayout = Layout::fromArray(
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

        $layout = $this->mapper->mapLayout($persistenceLayout);

        self::assertInstanceOf(NullLayoutType::class, $layout->getLayoutType());

        self::assertSame(1, $layout->getId());
        self::assertSame('My layout', $layout->getName());
        self::assertSame('My description', $layout->getDescription());
        self::assertSame(1447065813, $layout->getCreated()->getTimestamp());
        self::assertSame(1447065813, $layout->getModified()->getTimestamp());
        self::assertTrue($layout->isPublished());
        self::assertTrue($layout->isShared());

        self::assertNotEmpty($layout->getZones());
        self::assertContainsOnlyInstancesOf(APIZone::class, $layout->getZones());
    }
}
