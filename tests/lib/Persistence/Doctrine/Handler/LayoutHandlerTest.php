<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\ZoneUpdateStruct;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutHandlerTest extends TestCase
{
    use TestCaseTrait;
    use ExportObjectTrait;
    /**
     * @var \Netgen\Layouts\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\Layouts\Persistence\Handler\BlockHandlerInterface
     */
    private $blockHandler;

    /**
     * @var \Netgen\Layouts\Persistence\Handler\CollectionHandlerInterface
     */
    private $collectionHandler;

    public function setUp(): void
    {
        $this->createDatabase();

        $this->layoutHandler = $this->createLayoutHandler();
        $this->blockHandler = $this->createBlockHandler();
        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown(): void
    {
        $this->closeDatabase();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::__construct
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     */
    public function testLoadLayout(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED);

        self::assertSame(
            [
                'id' => 1,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My layout description',
                'shared' => false,
                'created' => 1447065813,
                'modified' => 1447065813,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr'],
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($layout)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     */
    public function testLoadLayoutThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "999999"');

        $this->layoutHandler->loadLayout(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getZoneSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     */
    public function testLoadZone(): void
    {
        $zone = $this->layoutHandler->loadZone(2, Value::STATUS_PUBLISHED, 'top');

        self::assertSame(
            [
                'identifier' => 'top',
                'layoutId' => 2,
                'rootBlockId' => 5,
                'linkedLayoutId' => 3,
                'linkedZoneIdentifier' => 'top',
                'status' => Value::STATUS_PUBLISHED,
            ],
            $this->exportObject($zone)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     */
    public function testLoadZoneThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find zone with identifier "non_existing"');

        $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'non_existing');
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadLayouts(): void
    {
        $layouts = $this->layoutHandler->loadLayouts();

        self::assertContainsOnlyInstancesOf(Layout::class, $layouts);

        self::assertSame(
            [
                [
                    'id' => 1,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'description' => 'My layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 2,
                    'type' => '4_zones_b',
                    'name' => 'My other layout',
                    'description' => 'My other layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 6,
                    'type' => '4_zones_b',
                    'name' => 'My sixth layout',
                    'description' => 'My sixth layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($layouts)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadLayoutsWithUnpublishedLayouts(): void
    {
        $layouts = $this->layoutHandler->loadLayouts(true);

        self::assertContainsOnlyInstancesOf(Layout::class, $layouts);

        self::assertSame(
            [
                [
                    'id' => 4,
                    'type' => '4_zones_b',
                    'name' => 'My fourth layout',
                    'description' => 'My fourth layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_DRAFT,
                ],
                [
                    'id' => 1,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'description' => 'My layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 2,
                    'type' => '4_zones_b',
                    'name' => 'My other layout',
                    'description' => 'My other layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 7,
                    'type' => '4_zones_b',
                    'name' => 'My seventh layout',
                    'description' => 'My seventh layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_DRAFT,
                ],
                [
                    'id' => 6,
                    'type' => '4_zones_b',
                    'name' => 'My sixth layout',
                    'description' => 'My sixth layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($layouts)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadLayoutsAndOffsetAndLimit(): void
    {
        $layouts = $this->layoutHandler->loadLayouts(false, 0, 2);

        self::assertContainsOnlyInstancesOf(Layout::class, $layouts);

        self::assertSame(
            [
                [
                    'id' => 1,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'description' => 'My layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 2,
                    'type' => '4_zones_b',
                    'name' => 'My other layout',
                    'description' => 'My other layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($layouts)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadLayoutsWithUnpublishedLayoutsAndOffsetAndLimit(): void
    {
        $layouts = $this->layoutHandler->loadLayouts(true, 0, 3);

        self::assertContainsOnlyInstancesOf(Layout::class, $layouts);

        self::assertSame(
            [
                [
                    'id' => 4,
                    'type' => '4_zones_b',
                    'name' => 'My fourth layout',
                    'description' => 'My fourth layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_DRAFT,
                ],
                [
                    'id' => 1,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'description' => 'My layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 2,
                    'type' => '4_zones_b',
                    'name' => 'My other layout',
                    'description' => 'My other layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($layouts)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::getLayoutsCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutsCount
     */
    public function testGetLayoutsCount(): void
    {
        self::assertSame(3, $this->layoutHandler->getLayoutsCount());
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::getLayoutsCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutsCount
     */
    public function testGetLayoutsCountWithUnpublishedLayouts(): void
    {
        self::assertSame(5, $this->layoutHandler->getLayoutsCount(true));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadSharedLayouts
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadSharedLayouts(): void
    {
        $layouts = $this->layoutHandler->loadSharedLayouts();

        self::assertContainsOnlyInstancesOf(Layout::class, $layouts);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'type' => '4_zones_b',
                    'name' => 'My fifth layout',
                    'description' => 'My fifth layout description',
                    'shared' => true,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 3,
                    'type' => '4_zones_b',
                    'name' => 'My third layout',
                    'description' => 'My third layout description',
                    'shared' => true,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($layouts)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::getSharedLayoutsCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutsCount
     */
    public function testGetSharedLayoutsCount(): void
    {
        self::assertSame(2, $this->layoutHandler->getSharedLayoutsCount());
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadAllLayouts
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutIds
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadAllLayouts(): void
    {
        $layouts = $this->layoutHandler->loadAllLayouts();

        self::assertContainsOnlyInstancesOf(Layout::class, $layouts);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'type' => '4_zones_b',
                    'name' => 'My fifth layout',
                    'description' => 'My fifth layout description',
                    'shared' => true,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 1,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'description' => 'My layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 2,
                    'type' => '4_zones_b',
                    'name' => 'My other layout',
                    'description' => 'My other layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 6,
                    'type' => '4_zones_b',
                    'name' => 'My sixth layout',
                    'description' => 'My sixth layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 3,
                    'type' => '4_zones_b',
                    'name' => 'My third layout',
                    'description' => 'My third layout description',
                    'shared' => true,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($layouts)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::getAllLayoutsCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutsCount
     */
    public function testGetAllLayoutsCount(): void
    {
        self::assertSame(5, $this->layoutHandler->getAllLayoutsCount());
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadRelatedLayouts
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadRelatedLayoutsData
     */
    public function testLoadRelatedLayouts(): void
    {
        $layouts = $this->layoutHandler->loadRelatedLayouts(
            $this->layoutHandler->loadLayout(3, Value::STATUS_PUBLISHED)
        );

        self::assertContainsOnlyInstancesOf(Layout::class, $layouts);

        self::assertSame(
            [
                [
                    'id' => 2,
                    'type' => '4_zones_b',
                    'name' => 'My other layout',
                    'description' => 'My other layout description',
                    'shared' => false,
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($layouts)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::getRelatedLayoutsCount
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCount(): void
    {
        $count = $this->layoutHandler->getRelatedLayoutsCount(
            $this->layoutHandler->loadLayout(3, Value::STATUS_PUBLISHED)
        );

        self::assertSame(1, $count);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutExists(): void
    {
        self::assertTrue($this->layoutHandler->layoutExists(1, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutNotExists(): void
    {
        self::assertFalse($this->layoutHandler->layoutExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutNotExistsInStatus(): void
    {
        self::assertFalse($this->layoutHandler->layoutExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::zoneExists
     */
    public function testZoneExists(): void
    {
        self::assertTrue(
            $this->layoutHandler->zoneExists(1, Value::STATUS_PUBLISHED, 'left')
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::zoneExists
     */
    public function testZoneNotExists(): void
    {
        self::assertFalse(
            $this->layoutHandler->zoneExists(1, Value::STATUS_PUBLISHED, 'non_existing')
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameExists(): void
    {
        self::assertTrue($this->layoutHandler->layoutNameExists('My layout'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExists(): void
    {
        self::assertFalse($this->layoutHandler->layoutNameExists('Non existent'));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedId(): void
    {
        self::assertFalse($this->layoutHandler->layoutNameExists('My layout', 1));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutZonesData
     */
    public function testLoadLayoutZones(): void
    {
        $zones = $this->layoutHandler->loadLayoutZones(
            $this->layoutHandler->loadLayout(2, Value::STATUS_PUBLISHED)
        );

        self::assertContainsOnlyInstancesOf(Zone::class, $zones);

        self::assertSame(
            [
                'bottom' => [
                    'identifier' => 'bottom',
                    'layoutId' => 2,
                    'rootBlockId' => 8,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_PUBLISHED,
                ],
                'left' => [
                    'identifier' => 'left',
                    'layoutId' => 2,
                    'rootBlockId' => 6,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_PUBLISHED,
                ],
                'right' => [
                    'identifier' => 'right',
                    'layoutId' => 2,
                    'rootBlockId' => 7,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_PUBLISHED,
                ],
                'top' => [
                    'identifier' => 'top',
                    'layoutId' => 2,
                    'rootBlockId' => 5,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'top',
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList($zones)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::updateZone
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateZone
     */
    public function testUpdateZone(): void
    {
        $zone = $this->layoutHandler->loadZone(1, Value::STATUS_DRAFT, 'top');
        $linkedZone = $this->layoutHandler->loadZone(3, Value::STATUS_PUBLISHED, 'top');

        $updatedZone = $this->layoutHandler->updateZone(
            $zone,
            ZoneUpdateStruct::fromArray(
                [
                    'linkedZone' => $linkedZone,
                ]
            )
        );

        self::assertSame(
            [
                'identifier' => 'top',
                'layoutId' => 1,
                'rootBlockId' => 1,
                'linkedLayoutId' => 3,
                'linkedZoneIdentifier' => 'top',
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedZone)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::updateZone
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateZone
     */
    public function testUpdateZoneWithResettingLinkedZone(): void
    {
        $zone = $this->layoutHandler->loadZone(1, Value::STATUS_DRAFT, 'left');

        $updatedZone = $this->layoutHandler->updateZone(
            $zone,
            ZoneUpdateStruct::fromArray(
                [
                    'linkedZone' => false,
                ]
            )
        );

        self::assertSame(
            [
                'identifier' => 'left',
                'layoutId' => 1,
                'rootBlockId' => 2,
                'linkedLayoutId' => null,
                'linkedZoneIdentifier' => null,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($updatedZone)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::createLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCreateLayout(): void
    {
        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->type = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->description = 'New description';
        $layoutCreateStruct->shared = true;
        $layoutCreateStruct->status = Value::STATUS_DRAFT;
        $layoutCreateStruct->mainLocale = 'en';

        $createdLayout = $this->layoutHandler->createLayout($layoutCreateStruct);

        self::assertSame(8, $createdLayout->id);
        self::assertSame('new_layout', $createdLayout->type);
        self::assertSame('New layout', $createdLayout->name);
        self::assertSame('New description', $createdLayout->description);
        self::assertSame(Value::STATUS_DRAFT, $createdLayout->status);
        self::assertTrue($createdLayout->shared);
        self::assertSame('en', $createdLayout->mainLocale);
        self::assertGreaterThan(0, $createdLayout->created);
        self::assertGreaterThan(0, $createdLayout->modified);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::createLayoutTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCreateLayoutTranslation(): void
    {
        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $layout = $this->layoutHandler->createLayoutTranslation($originalLayout, 'de', 'en');

        self::assertSame('en', $layout->mainLocale);
        self::assertSame(['en', 'hr', 'de'], $layout->availableLocales);
        self::assertSame($originalLayout->created, $layout->created);
        self::assertGreaterThan($originalLayout->modified, $layout->modified);

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($layout);
        foreach ($layoutBlocks as $layoutBlock) {
            $layoutBlock->isTranslatable ?
                self::assertContains('de', $layoutBlock->availableLocales) :
                self::assertNotContains('de', $layoutBlock->availableLocales);
        }
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::createLayoutTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCreateLayoutTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Layout already has the provided locale.');

        $this->layoutHandler->createLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'en',
            'hr'
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::createLayoutTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCreateLayoutTranslationThrowsBadStateExceptionWithNonExistingSourceLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "sourceLocale" has an invalid state. Layout does not have the provided source locale.');

        $this->layoutHandler->createLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'de',
            'fr'
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::setMainTranslation
     */
    public function testSetMainTranslation(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->setMainTranslation($layout, 'hr');

        self::assertSame('hr', $updatedLayout->mainLocale);
        self::assertSame($layout->created, $updatedLayout->created);
        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($updatedLayout);
        foreach ($layoutBlocks as $layoutBlock) {
            self::assertSame('hr', $layoutBlock->mainLocale);
        }
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::setMainTranslation
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "mainLocale" has an invalid state. Layout does not have the provided locale.');

        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $this->layoutHandler->setMainTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::createZone
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createZone
     */
    public function testCreateZone(): void
    {
        $zoneCreateStruct = new ZoneCreateStruct();
        $zoneCreateStruct->identifier = 'new_zone';
        $zoneCreateStruct->linkedLayoutId = 3;
        $zoneCreateStruct->linkedZoneIdentifier = 'linked_zone';

        $createdZone = $this->layoutHandler->createZone(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            $zoneCreateStruct
        );

        self::assertSame(1, $createdZone->layoutId);
        self::assertSame(Value::STATUS_DRAFT, $createdZone->status);
        self::assertSame('new_zone', $createdZone->identifier);
        self::assertSame(39, $createdZone->rootBlockId);
        self::assertSame(3, $createdZone->linkedLayoutId);
        self::assertSame('linked_zone', $createdZone->linkedZoneIdentifier);

        $rootBlock = $this->blockHandler->loadBlock(39, Value::STATUS_DRAFT);

        self::assertSame(
            [
                'id' => 39,
                'layoutId' => $createdZone->layoutId,
                'depth' => 0,
                'path' => '/39/',
                'parentId' => null,
                'placeholder' => null,
                'position' => null,
                'definitionIdentifier' => '',
                'parameters' => ['en' => []],
                'config' => [],
                'viewType' => '',
                'itemViewType' => '',
                'name' => '',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_DRAFT,
            ],
            $this->exportObject($rootBlock)
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::updateLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateLayout
     */
    public function testUpdateLayout(): void
    {
        $layoutUpdateStruct = new LayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';
        $layoutUpdateStruct->modified = 123;
        $layoutUpdateStruct->description = 'New description';

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->updateLayout(
            $originalLayout,
            $layoutUpdateStruct
        );

        self::assertSame('New name', $updatedLayout->name);
        self::assertSame('New description', $updatedLayout->description);
        self::assertSame($originalLayout->created, $updatedLayout->created);
        self::assertSame(123, $updatedLayout->modified);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::updateLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateLayout
     */
    public function testUpdateLayoutWithDefaultValues(): void
    {
        $layoutUpdateStruct = new LayoutUpdateStruct();

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->updateLayout(
            $originalLayout,
            $layoutUpdateStruct
        );

        self::assertSame('My layout', $updatedLayout->name);
        self::assertSame('My layout description', $updatedLayout->description);
        self::assertSame($originalLayout->created, $updatedLayout->created);
        self::assertGreaterThan($originalLayout->modified, $updatedLayout->modified);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::copyLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::createZone
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCopyLayout(): void
    {
        // Link the zone before copying, to make sure those are copied too
        $this->layoutHandler->updateZone(
            $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'left'),
            ZoneUpdateStruct::fromArray(
                [
                    'linkedZone' => $this->layoutHandler->loadZone(3, Value::STATUS_PUBLISHED, 'left'),
                ]
            )
        );

        $copyStruct = new LayoutCopyStruct();
        $copyStruct->name = 'New name';
        $copyStruct->description = 'New description';

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED);
        $copiedLayout = $this->layoutHandler->copyLayout($originalLayout, $copyStruct);

        self::assertSame(8, $copiedLayout->id);
        self::assertSame('4_zones_a', $copiedLayout->type);
        self::assertSame('New name', $copiedLayout->name);
        self::assertSame('New description', $copiedLayout->description);
        self::assertSame(Value::STATUS_PUBLISHED, $copiedLayout->status);
        self::assertFalse($copiedLayout->shared);
        self::assertSame('en', $copiedLayout->mainLocale);
        self::assertSame(['en', 'hr'], $copiedLayout->availableLocales);

        self::assertGreaterThan($originalLayout->created, $copiedLayout->created);
        self::assertSame($copiedLayout->created, $copiedLayout->modified);

        self::assertSame(
            [
                'bottom' => [
                    'identifier' => 'bottom',
                    'layoutId' => $copiedLayout->id,
                    'rootBlockId' => 39,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_PUBLISHED,
                ],
                'left' => [
                    'identifier' => 'left',
                    'layoutId' => $copiedLayout->id,
                    'rootBlockId' => 40,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'left',
                    'status' => Value::STATUS_PUBLISHED,
                ],
                'right' => [
                    'identifier' => 'right',
                    'layoutId' => $copiedLayout->id,
                    'rootBlockId' => 42,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_PUBLISHED,
                ],
                'top' => [
                    'identifier' => 'top',
                    'layoutId' => $copiedLayout->id,
                    'rootBlockId' => 45,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList(
                $this->layoutHandler->loadLayoutZones($copiedLayout)
            )
        );

        self::assertSame(
            [
                [
                    'id' => 41,
                    'layoutId' => $copiedLayout->id,
                    'depth' => 1,
                    'path' => '/40/41/',
                    'parentId' => 40,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [
                        'key' => [
                            'param1' => false,
                        ],
                    ],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My other block',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList(
                $this->blockHandler->loadChildBlocks(
                    $this->blockHandler->loadBlock(40, Value::STATUS_PUBLISHED)
                )
            )
        );

        self::assertSame(
            [
                [
                    'id' => 43,
                    'layoutId' => $copiedLayout->id,
                    'depth' => 1,
                    'path' => '/42/43/',
                    'parentId' => 42,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard_with_intro',
                    'name' => 'My published block',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_PUBLISHED,
                ],
                [
                    'id' => 44,
                    'layoutId' => $copiedLayout->id,
                    'depth' => 1,
                    'path' => '/42/44/',
                    'parentId' => 42,
                    'placeholder' => 'root',
                    'position' => 1,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My fourth block',
                    'isTranslatable' => false,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_PUBLISHED,
                ],
            ],
            $this->exportObjectList(
                $this->blockHandler->loadChildBlocks(
                    $this->blockHandler->loadBlock(42, Value::STATUS_PUBLISHED)
                )
            )
        );

        // Verify that collections were copied
        $this->collectionHandler->loadCollection(7, Value::STATUS_PUBLISHED);
        $this->collectionHandler->loadCollection(8, Value::STATUS_PUBLISHED);

        // Verify the state of the collection references

        // First block
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(41, Value::STATUS_PUBLISHED)
        );

        self::assertCount(0, $references);

        // Second block
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(43, Value::STATUS_PUBLISHED)
        );

        self::assertCount(2, $references);
        self::assertContains($references[0]->collectionId, [7, 8]);
        self::assertContains($references[1]->collectionId, [7, 8]);

        // Third block
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(44, Value::STATUS_PUBLISHED)
        );

        self::assertCount(1, $references);
        self::assertSame($references[0]->collectionId, 9);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::changeLayoutType
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createZone
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteZone
     */
    public function testChangeLayoutType(): void
    {
        // Link the zone before copying, to make sure those are removed
        $this->layoutHandler->updateZone(
            $this->layoutHandler->loadZone(1, Value::STATUS_DRAFT, 'left'),
            ZoneUpdateStruct::fromArray(
                [
                    'linkedZone' => $this->layoutHandler->loadZone(3, Value::STATUS_PUBLISHED, 'left'),
                ]
            )
        );

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->changeLayoutType(
            $originalLayout,
            '4_zones_b',
            [
                'top' => ['left', 'right'],
                'left' => [],
                'right' => [],
                'bottom' => [],
            ]
        );

        self::assertSame(1, $updatedLayout->id);
        self::assertSame('4_zones_b', $updatedLayout->type);
        self::assertSame('My layout', $updatedLayout->name);
        self::assertSame('My layout description', $updatedLayout->description);
        self::assertSame(Value::STATUS_DRAFT, $updatedLayout->status);
        self::assertFalse($updatedLayout->shared);

        self::assertSame($originalLayout->created, $updatedLayout->created);
        self::assertGreaterThan($originalLayout->modified, $updatedLayout->modified);

        self::assertSame(
            [
                'bottom' => [
                    'identifier' => 'bottom',
                    'layoutId' => $updatedLayout->id,
                    'rootBlockId' => 42,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_DRAFT,
                ],
                'left' => [
                    'identifier' => 'left',
                    'layoutId' => $updatedLayout->id,
                    'rootBlockId' => 40,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_DRAFT,
                ],
                'right' => [
                    'identifier' => 'right',
                    'layoutId' => $updatedLayout->id,
                    'rootBlockId' => 41,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_DRAFT,
                ],
                'top' => [
                    'identifier' => 'top',
                    'layoutId' => $updatedLayout->id,
                    'rootBlockId' => 39,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_DRAFT,
                ],
            ],
            $this->exportObjectList(
                $this->layoutHandler->loadLayoutZones($updatedLayout)
            )
        );

        self::assertSame(
            [
                [
                    'id' => 32,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/39/32/',
                    'parentId' => 39,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [
                        'key' => [
                            'param1' => false,
                        ],
                    ],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My other block',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_DRAFT,
                ],
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/39/31/',
                    'parentId' => 39,
                    'placeholder' => 'root',
                    'position' => 1,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 2,
                            'css_class' => 'css-class',
                            'css_id' => 'css-id',
                        ],
                        'hr' => [
                            'css_class' => 'css-class-hr',
                            'css_id' => 'css-id',
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'list',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_DRAFT,
                ],
                [
                    'id' => 35,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/39/35/',
                    'parentId' => 39,
                    'placeholder' => 'root',
                    'position' => 2,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My fourth block',
                    'isTranslatable' => false,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_DRAFT,
                ],
            ],
            $this->exportObjectList(
                $this->blockHandler->loadChildBlocks(
                    $this->blockHandler->loadBlock(39, Value::STATUS_DRAFT)
                )
            )
        );

        self::assertEmpty(
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(40, Value::STATUS_DRAFT)
            )
        );

        self::assertEmpty(
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(41, Value::STATUS_DRAFT)
            )
        );

        self::assertEmpty(
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(42, Value::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::createLayoutStatus
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     */
    public function testCreateLayoutStatus(): void
    {
        // Link the zone before copying, to make sure those are copied too
        $this->layoutHandler->updateZone(
            $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'left'),
            ZoneUpdateStruct::fromArray(
                [
                    'linkedZone' => $this->layoutHandler->loadZone(3, Value::STATUS_PUBLISHED, 'left'),
                ]
            )
        );

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED);
        $copiedLayout = $this->layoutHandler->createLayoutStatus(
            $originalLayout,
            Value::STATUS_ARCHIVED
        );

        self::assertSame(1, $copiedLayout->id);
        self::assertSame('4_zones_a', $copiedLayout->type);
        self::assertSame('My layout', $copiedLayout->name);
        self::assertSame('My layout description', $copiedLayout->description);
        self::assertSame(Value::STATUS_ARCHIVED, $copiedLayout->status);
        self::assertFalse($copiedLayout->shared);
        self::assertSame('en', $copiedLayout->mainLocale);
        self::assertSame(['en', 'hr'], $copiedLayout->availableLocales);

        self::assertSame($originalLayout->created, $copiedLayout->created);
        self::assertGreaterThan($originalLayout->modified, $copiedLayout->modified);

        self::assertSame(
            [
                'bottom' => [
                    'identifier' => 'bottom',
                    'layoutId' => 1,
                    'rootBlockId' => 4,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_ARCHIVED,
                ],
                'left' => [
                    'identifier' => 'left',
                    'layoutId' => 1,
                    'rootBlockId' => 2,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'left',
                    'status' => Value::STATUS_ARCHIVED,
                ],
                'right' => [
                    'identifier' => 'right',
                    'layoutId' => 1,
                    'rootBlockId' => 3,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_ARCHIVED,
                ],
                'top' => [
                    'identifier' => 'top',
                    'layoutId' => 1,
                    'rootBlockId' => 1,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                    'status' => Value::STATUS_ARCHIVED,
                ],
            ],
            $this->exportObjectList(
                $this->layoutHandler->loadLayoutZones($copiedLayout)
            )
        );

        self::assertSame(
            [
                [
                    'id' => 32,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/2/32/',
                    'parentId' => 2,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [
                        'key' => [
                            'param1' => false,
                        ],
                    ],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My other block',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_ARCHIVED,
                ],
            ],
            $this->exportObjectList(
                $this->blockHandler->loadChildBlocks(
                    $this->blockHandler->loadBlock(2, Value::STATUS_ARCHIVED)
                )
            )
        );

        self::assertSame(
            [
                [
                    'id' => 31,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/31/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 0,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                        'hr' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard_with_intro',
                    'name' => 'My published block',
                    'isTranslatable' => true,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_ARCHIVED,
                ],
                [
                    'id' => 35,
                    'layoutId' => 1,
                    'depth' => 1,
                    'path' => '/3/35/',
                    'parentId' => 3,
                    'placeholder' => 'root',
                    'position' => 1,
                    'definitionIdentifier' => 'list',
                    'parameters' => [
                        'en' => [
                            'number_of_columns' => 3,
                        ],
                    ],
                    'config' => [],
                    'viewType' => 'grid',
                    'itemViewType' => 'standard',
                    'name' => 'My fourth block',
                    'isTranslatable' => false,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en'],
                    'alwaysAvailable' => true,
                    'status' => Value::STATUS_ARCHIVED,
                ],
            ],
            $this->exportObjectList(
                $this->blockHandler->loadChildBlocks(
                    $this->blockHandler->loadBlock(3, Value::STATUS_ARCHIVED)
                )
            )
        );

        // Verify that the collection status was copied
        $this->collectionHandler->loadCollection(2, Value::STATUS_ARCHIVED);

        // Verify the state of the collection references
        $archivedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(31, Value::STATUS_ARCHIVED)
        );

        self::assertCount(2, $archivedReferences);
        self::assertContains($archivedReferences[0]->collectionId, [2, 3]);
        self::assertContains($archivedReferences[1]->collectionId, [2, 3]);

        // Second block
        $archivedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(35, Value::STATUS_ARCHIVED)
        );

        self::assertCount(1, $archivedReferences);
        self::assertSame(4, $archivedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutZones
     */
    public function testDeleteLayout(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find layout with identifier "1"');

        $this->layoutHandler->deleteLayout(1);

        // Verify that we don't have the collections that were related to the layout
        try {
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
            $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
            self::fail('Collections not deleted after deleting the layout.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that we don't have the layout any more
        $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayout
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutZones
     */
    public function testDeleteLayoutInOneStatus(): void
    {
        $this->layoutHandler->deleteLayout(1, Value::STATUS_DRAFT);

        // Verify that we don't have the layout in deleted status any more
        try {
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
            self::fail('Layout not deleted after deleting it in one status.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that NOT all layout statuses are deleted
        $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED);

        // Verify that we don't have the collection that was related to layout in deleted status any more
        try {
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
            self::fail('Collection not deleted after deleting layout in one status.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that NOT all collections are deleted
        $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);
        $this->collectionHandler->loadCollection(4, Value::STATUS_PUBLISHED);

        // Verify the state of the collection references
        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(31, Value::STATUS_PUBLISHED)
        );

        self::assertCount(2, $publishedReferences);
        self::assertContains($publishedReferences[0]->collectionId, [2, 3]);
        self::assertContains($publishedReferences[1]->collectionId, [2, 3]);

        // Second block
        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(35, Value::STATUS_PUBLISHED)
        );

        self::assertCount(1, $publishedReferences);
        self::assertSame(4, $publishedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::updateLayoutModifiedDate
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     */
    public function testDeleteLayoutTranslation(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->deleteLayoutTranslation($layout, 'hr');

        self::assertSame($layout->created, $updatedLayout->created);
        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        self::assertSame('en', $updatedLayout->mainLocale);
        self::assertSame(['en'], $updatedLayout->availableLocales);

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($updatedLayout);
        foreach ($layoutBlocks as $layoutBlock) {
            self::assertNotContains('hr', $layoutBlock->availableLocales);
        }
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::updateLayoutModifiedDate
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     */
    public function testDeleteLayoutTranslationWithInconsistentBlock(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "31"');

        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);

        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);

        $block = $this->blockHandler->setMainTranslation($block, 'hr');
        $this->blockHandler->deleteBlockTranslation($block, 'en');

        $updatedLayout = $this->layoutHandler->deleteLayoutTranslation($layout, 'hr');

        self::assertSame($layout->created, $updatedLayout->created);
        self::assertGreaterThan($layout->modified, $updatedLayout->modified);

        self::assertSame('en', $updatedLayout->mainLocale);
        self::assertSame(['en'], $updatedLayout->availableLocales);

        $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     */
    public function testDeleteLayoutTranslationWithNonExistingLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Layout does not have the provided locale.');

        $this->layoutHandler->deleteLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'de'
        );
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     */
    public function testDeleteLayoutTranslationWithMainLocale(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "locale" has an invalid state. Main translation cannot be removed from the layout.');

        $this->layoutHandler->deleteLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'en'
        );
    }
}
