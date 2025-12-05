<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Layout\Zone as APIZone;
use Netgen\Layouts\Core\Mapper\LayoutMapper;
use Netgen\Layouts\Layout\Type\NullLayoutType;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use Netgen\Layouts\Tests\Core\CoreTestCase;

abstract class LayoutMapperTestBase extends CoreTestCase
{
    private LayoutMapper $mapper;

    final protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createLayoutMapper();
    }

    final public function testMapZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'rootBlockId' => 3,
                'linkedLayoutUuid' => 'd8e55af7-cf62-5f28-ae15-331b457d82e9',
                'linkedZoneIdentifier' => 'right',
            ],
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->identifier);
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $zone->layoutId->toString());
        self::assertTrue($zone->isPublished);
        self::assertInstanceOf(APIZone::class, $zone->linkedZone);
        self::assertTrue($zone->linkedZone->isPublished);
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $zone->linkedZone->layoutId->toString());
        self::assertSame('right', $zone->linkedZone->identifier);
    }

    final public function testMapZoneWithNoLinkedZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'rootBlockId' => 3,
                'linkedLayoutUuid' => null,
                'linkedZoneIdentifier' => null,
            ],
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->identifier);
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $zone->layoutId->toString());
        self::assertTrue($zone->isPublished);
        self::assertNull($zone->linkedZone);
    }

    final public function testMapZoneWithNonExistingLinkedZone(): void
    {
        $persistenceZone = Zone::fromArray(
            [
                'identifier' => 'right',
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'rootBlockId' => 3,
                'linkedLayoutUuid' => 'd8e55af7-cf62-5f28-ae15-331b457d82e9',
                'linkedZoneIdentifier' => 'unknown',
            ],
        );

        $zone = $this->mapper->mapZone($persistenceZone);

        self::assertSame('right', $zone->identifier);
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $zone->layoutId->toString());
        self::assertTrue($zone->isPublished);
        self::assertNull($zone->linkedZone);
    }

    final public function testMapLayout(): void
    {
        $persistenceLayout = Layout::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'type' => 'test_layout_1',
                'name' => 'My layout',
                'description' => 'My description',
                'created' => 1_447_065_813,
                'modified' => 1_447_065_813,
                'status' => PersistenceStatus::Published,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'isShared' => true,
            ],
        );

        $layout = $this->mapper->mapLayout($persistenceLayout);

        self::assertSame(
            $this->layoutTypeRegistry->getLayoutType('test_layout_1'),
            $layout->layoutType,
        );

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $layout->id->toString());
        self::assertSame('My layout', $layout->name);
        self::assertSame('My description', $layout->description);
        self::assertSame(1_447_065_813, $layout->created->getTimestamp());
        self::assertSame(1_447_065_813, $layout->modified->getTimestamp());
        self::assertTrue($layout->isPublished);
        self::assertTrue($layout->isShared);
        self::assertCount(4, $layout->zones);
    }

    final public function testMapLayoutWithInvalidLayoutType(): void
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
                'status' => PersistenceStatus::Published,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'isShared' => true,
            ],
        );

        $layout = $this->mapper->mapLayout($persistenceLayout);

        self::assertInstanceOf(NullLayoutType::class, $layout->layoutType);

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $layout->id->toString());
        self::assertSame('My layout', $layout->name);
        self::assertSame('My description', $layout->description);
        self::assertSame(1_447_065_813, $layout->created->getTimestamp());
        self::assertSame(1_447_065_813, $layout->modified->getTimestamp());
        self::assertTrue($layout->isPublished);
        self::assertTrue($layout->isShared);
        self::assertCount(4, $layout->zones);
    }
}
