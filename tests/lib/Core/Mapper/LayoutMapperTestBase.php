<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Layout\Zone as APIZone;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Core\Mapper\LayoutMapper;
use Netgen\Layouts\Layout\Type\NullLayoutType;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Tests\Core\CoreTestCase;

abstract class LayoutMapperTestBase extends CoreTestCase
{
    private LayoutMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createLayoutMapper();
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\LayoutMapper::mapZone
     */
    public function testMapZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutUuid' => 'd8e55af7-cf62-5f28-ae15-331b457d82e9',
                'linkedZoneIdentifier' => 'right',
            ],
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->getIdentifier());
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $zone->getLayoutId()->toString());
        self::assertTrue($zone->isPublished());
        self::assertInstanceOf(APIZone::class, $zone->getLinkedZone());
        self::assertTrue($zone->getLinkedZone()->isPublished());
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $zone->getLinkedZone()->getLayoutId()->toString());
        self::assertSame('right', $zone->getLinkedZone()->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutMapper::mapZone
     */
    public function testMapZoneWithNoLinkedZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutUuid' => null,
                'linkedZoneIdentifier' => null,
            ],
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->getIdentifier());
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $zone->getLayoutId()->toString());
        self::assertTrue($zone->isPublished());
        self::assertNull($zone->getLinkedZone());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutMapper::mapZone
     */
    public function testMapZoneWithNonExistingLinkedZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 3,
                'linkedLayoutUuid' => 'd8e55af7-cf62-5f28-ae15-331b457d82e9',
                'linkedZoneIdentifier' => 'unknown',
            ],
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->getIdentifier());
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $zone->getLayoutId()->toString());
        self::assertTrue($zone->isPublished());
        self::assertNull($zone->getLinkedZone());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutMapper::mapLayout
     */
    public function testMapLayout(): void
    {
        $persistenceLayout = Layout::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My description',
                'created' => 1_447_065_813,
                'modified' => 1_447_065_813,
                'status' => Value::STATUS_PUBLISHED,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'shared' => true,
            ],
        );

        $layout = $this->mapper->mapLayout($persistenceLayout);

        self::assertSame(
            $this->layoutTypeRegistry->getLayoutType('4_zones_a'),
            $layout->getLayoutType(),
        );

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $layout->getId()->toString());
        self::assertSame('My layout', $layout->getName());
        self::assertSame('My description', $layout->getDescription());
        self::assertSame(1_447_065_813, $layout->getCreated()->getTimestamp());
        self::assertSame(1_447_065_813, $layout->getModified()->getTimestamp());
        self::assertTrue($layout->isPublished());
        self::assertTrue($layout->isShared());
        self::assertCount(4, $layout->getZones());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutMapper::mapLayout
     */
    public function testMapLayoutWithInvalidLayoutType(): void
    {
        $persistenceLayout = Layout::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'type' => 'unknown',
                'name' => 'My layout',
                'description' => 'My description',
                'created' => 1_447_065_813,
                'modified' => 1_447_065_813,
                'status' => Value::STATUS_PUBLISHED,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'shared' => true,
            ],
        );

        $layout = $this->mapper->mapLayout($persistenceLayout);

        self::assertInstanceOf(NullLayoutType::class, $layout->getLayoutType());

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $layout->getId()->toString());
        self::assertSame('My layout', $layout->getName());
        self::assertSame('My description', $layout->getDescription());
        self::assertSame(1_447_065_813, $layout->getCreated()->getTimestamp());
        self::assertSame(1_447_065_813, $layout->getModified()->getTimestamp());
        self::assertTrue($layout->isPublished());
        self::assertTrue($layout->isShared());
        self::assertCount(4, $layout->getZones());
    }
}
