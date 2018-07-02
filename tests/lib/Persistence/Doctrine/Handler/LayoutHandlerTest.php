<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class LayoutHandlerTest extends TestCase
{
    use TestCaseTrait;
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutHandlerInterface
     */
    private $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandlerInterface
     */
    private $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandlerInterface
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     */
    public function testLoadLayout(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED);

        $this->assertInstanceOf(Layout::class, $layout);

        $this->assertSame(
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "999999"
     */
    public function testLoadLayoutThrowsNotFoundException(): void
    {
        $this->layoutHandler->loadLayout(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getZoneSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     */
    public function testLoadZone(): void
    {
        $zone = $this->layoutHandler->loadZone(2, Value::STATUS_PUBLISHED, 'top');

        $this->assertInstanceOf(Zone::class, $zone);

        $this->assertSame(
            [
                'identifier' => 'top',
                'layoutId' => 2,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 5,
                'linkedLayoutId' => 3,
                'linkedZoneIdentifier' => 'top',
            ],
            $this->exportObject($zone)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "non_existing"
     */
    public function testLoadZoneThrowsNotFoundException(): void
    {
        $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadLayouts(): void
    {
        $layouts = $this->layoutHandler->loadLayouts();

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
        }

        $this->assertSame(
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadLayoutsWithUnpublishedLayouts(): void
    {
        $layouts = $this->layoutHandler->loadLayouts(true);

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
        }

        $this->assertSame(
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadSharedLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadSharedLayouts(): void
    {
        $layouts = $this->layoutHandler->loadSharedLayouts();

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
        }

        $this->assertSame(
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadRelatedLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadRelatedLayoutsData
     */
    public function testLoadRelatedLayouts(): void
    {
        $layouts = $this->layoutHandler->loadRelatedLayouts(
            $this->layoutHandler->loadLayout(3, Value::STATUS_PUBLISHED)
        );

        foreach ($layouts as $layout) {
            $this->assertInstanceOf(Layout::class, $layout);
        }

        $this->assertSame(
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::getRelatedLayoutsCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCount(): void
    {
        $count = $this->layoutHandler->getRelatedLayoutsCount(
            $this->layoutHandler->loadLayout(3, Value::STATUS_PUBLISHED)
        );

        $this->assertSame(1, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutExists(): void
    {
        $this->assertTrue($this->layoutHandler->layoutExists(1, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutNotExists(): void
    {
        $this->assertFalse($this->layoutHandler->layoutExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutNotExistsInStatus(): void
    {
        $this->assertFalse($this->layoutHandler->layoutExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::zoneExists
     */
    public function testZoneExists(): void
    {
        $this->assertTrue(
            $this->layoutHandler->zoneExists(1, Value::STATUS_PUBLISHED, 'left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::zoneExists
     */
    public function testZoneNotExists(): void
    {
        $this->assertFalse(
            $this->layoutHandler->zoneExists(1, Value::STATUS_PUBLISHED, 'non_existing')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameExists(): void
    {
        $this->assertTrue($this->layoutHandler->layoutNameExists('My layout'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExists(): void
    {
        $this->assertFalse($this->layoutHandler->layoutNameExists('Non existent'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedId(): void
    {
        $this->assertFalse($this->layoutHandler->layoutNameExists('My layout', 1));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutZonesData
     */
    public function testLoadLayoutZones(): void
    {
        $zones = $this->layoutHandler->loadLayoutZones(
            $this->layoutHandler->loadLayout(2, Value::STATUS_PUBLISHED)
        );

        foreach ($zones as $zone) {
            $this->assertInstanceOf(Zone::class, $zone);
        }

        $this->assertSame(
            [
                'bottom' => [
                    'identifier' => 'bottom',
                    'layoutId' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 8,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'left' => [
                    'identifier' => 'left',
                    'layoutId' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 6,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'right' => [
                    'identifier' => 'right',
                    'layoutId' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 7,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'top' => [
                    'identifier' => 'top',
                    'layoutId' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 5,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'top',
                ],
            ],
            $this->exportObjectList($zones)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateZone
     */
    public function testUpdateZone(): void
    {
        $zone = $this->layoutHandler->loadZone(1, Value::STATUS_DRAFT, 'top');
        $linkedZone = $this->layoutHandler->loadZone(3, Value::STATUS_PUBLISHED, 'top');

        $updatedZone = $this->layoutHandler->updateZone(
            $zone,
            new ZoneUpdateStruct(
                [
                    'linkedZone' => $linkedZone,
                ]
            )
        );

        $this->assertInstanceOf(Zone::class, $updatedZone);

        $this->assertSame(
            [
                'identifier' => 'top',
                'layoutId' => 1,
                'status' => Value::STATUS_DRAFT,
                'rootBlockId' => 1,
                'linkedLayoutId' => 3,
                'linkedZoneIdentifier' => 'top',
            ],
            $this->exportObject($updatedZone)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateZone
     */
    public function testUpdateZoneWithResettingLinkedZone(): void
    {
        $zone = $this->layoutHandler->loadZone(1, Value::STATUS_DRAFT, 'left');

        $updatedZone = $this->layoutHandler->updateZone(
            $zone,
            new ZoneUpdateStruct(
                [
                    'linkedZone' => false,
                ]
            )
        );

        $this->assertInstanceOf(Zone::class, $updatedZone);

        $this->assertSame(
            [
                'identifier' => 'left',
                'layoutId' => 1,
                'status' => Value::STATUS_DRAFT,
                'rootBlockId' => 2,
                'linkedLayoutId' => null,
                'linkedZoneIdentifier' => null,
            ],
            $this->exportObject($updatedZone)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
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

        $this->assertInstanceOf(Layout::class, $createdLayout);

        $this->assertSame(8, $createdLayout->id);
        $this->assertSame('new_layout', $createdLayout->type);
        $this->assertSame('New layout', $createdLayout->name);
        $this->assertSame('New description', $createdLayout->description);
        $this->assertSame(Value::STATUS_DRAFT, $createdLayout->status);
        $this->assertTrue($createdLayout->shared);
        $this->assertSame('en', $createdLayout->mainLocale);

        $this->assertInternalType('int', $createdLayout->created);
        $this->assertGreaterThan(0, $createdLayout->created);

        $this->assertInternalType('int', $createdLayout->modified);
        $this->assertGreaterThan(0, $createdLayout->modified);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCreateLayoutTranslation(): void
    {
        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $layout = $this->layoutHandler->createLayoutTranslation($originalLayout, 'de', 'en');

        $this->assertInstanceOf(Layout::class, $layout);

        $this->assertSame('en', $layout->mainLocale);
        $this->assertSame(['en', 'hr', 'de'], $layout->availableLocales);
        $this->assertSame($originalLayout->created, $layout->created);
        $this->assertGreaterThan($originalLayout->modified, $layout->modified);

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($layout);
        foreach ($layoutBlocks as $layoutBlock) {
            $layoutBlock->isTranslatable ?
                $this->assertContains('de', $layoutBlock->availableLocales) :
                $this->assertNotContains('de', $layoutBlock->availableLocales);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Layout already has the provided locale.
     */
    public function testCreateLayoutTranslationThrowsBadStateExceptionWithExistingLocale(): void
    {
        $this->layoutHandler->createLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'en',
            'hr'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "sourceLocale" has an invalid state. Layout does not have the provided source locale.
     */
    public function testCreateLayoutTranslationThrowsBadStateExceptionWithNonExistingSourceLocale(): void
    {
        $this->layoutHandler->createLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'de',
            'fr'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::setMainTranslation
     */
    public function testSetMainTranslation(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->setMainTranslation($layout, 'hr');

        $this->assertSame('hr', $updatedLayout->mainLocale);
        $this->assertSame($layout->created, $updatedLayout->created);
        $this->assertGreaterThan($layout->modified, $updatedLayout->modified);

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($updatedLayout);
        foreach ($layoutBlocks as $layoutBlock) {
            $this->assertSame('hr', $layoutBlock->mainLocale);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "mainLocale" has an invalid state. Layout does not have the provided locale.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $this->layoutHandler->setMainTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createZone
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

        $this->assertInstanceOf(Zone::class, $createdZone);

        $this->assertSame(1, $createdZone->layoutId);
        $this->assertSame(Value::STATUS_DRAFT, $createdZone->status);
        $this->assertSame('new_zone', $createdZone->identifier);
        $this->assertSame(39, $createdZone->rootBlockId);
        $this->assertSame(3, $createdZone->linkedLayoutId);
        $this->assertSame('linked_zone', $createdZone->linkedZoneIdentifier);

        $rootBlock = $this->blockHandler->loadBlock(39, Value::STATUS_DRAFT);

        $this->assertInstanceOf(Block::class, $rootBlock);

        $this->assertSame(
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateLayout
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

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertSame('New name', $updatedLayout->name);
        $this->assertSame('New description', $updatedLayout->description);
        $this->assertSame($originalLayout->created, $updatedLayout->created);
        $this->assertSame(123, $updatedLayout->modified);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateLayout
     */
    public function testUpdateLayoutWithDefaultValues(): void
    {
        $layoutUpdateStruct = new LayoutUpdateStruct();

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->updateLayout(
            $originalLayout,
            $layoutUpdateStruct
        );

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertSame('My layout', $updatedLayout->name);
        $this->assertSame('My layout description', $updatedLayout->description);
        $this->assertSame($originalLayout->created, $updatedLayout->created);
        $this->assertGreaterThan($originalLayout->modified, $updatedLayout->modified);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::copyLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCopyLayout(): void
    {
        // Link the zone before copying, to make sure those are copied too
        $this->layoutHandler->updateZone(
            $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'left'),
            new ZoneUpdateStruct(
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

        $this->assertInstanceOf(Layout::class, $copiedLayout);

        $this->assertSame(8, $copiedLayout->id);
        $this->assertSame('4_zones_a', $copiedLayout->type);
        $this->assertSame('New name', $copiedLayout->name);
        $this->assertSame('New description', $copiedLayout->description);
        $this->assertSame(Value::STATUS_PUBLISHED, $copiedLayout->status);
        $this->assertFalse($copiedLayout->shared);
        $this->assertSame('en', $copiedLayout->mainLocale);
        $this->assertSame(['en', 'hr'], $copiedLayout->availableLocales);

        $this->assertGreaterThan($originalLayout->created, $copiedLayout->created);
        $this->assertSame($copiedLayout->created, $copiedLayout->modified);

        $this->assertSame(
            [
                'bottom' => [
                    'identifier' => 'bottom',
                    'layoutId' => $copiedLayout->id,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 39,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'left' => [
                    'identifier' => 'left',
                    'layoutId' => $copiedLayout->id,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 40,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'left',
                ],
                'right' => [
                    'identifier' => 'right',
                    'layoutId' => $copiedLayout->id,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 42,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'top' => [
                    'identifier' => 'top',
                    'layoutId' => $copiedLayout->id,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 45,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
            ],
            $this->exportObjectList(
                $this->layoutHandler->loadLayoutZones($copiedLayout)
            )
        );

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertCount(0, $references);

        // Second block
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(43, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(2, $references);
        $this->assertContains($references[0]->collectionId, [7, 8]);
        $this->assertContains($references[1]->collectionId, [7, 8]);

        // Third block
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(44, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(1, $references);
        $this->assertSame($references[0]->collectionId, 9);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::changeLayoutType
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteZone
     */
    public function testChangeLayoutType(): void
    {
        // Link the zone before copying, to make sure those are removed
        $this->layoutHandler->updateZone(
            $this->layoutHandler->loadZone(1, Value::STATUS_DRAFT, 'left'),
            new ZoneUpdateStruct(
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

        $this->assertInstanceOf(Layout::class, $updatedLayout);

        $this->assertSame(1, $updatedLayout->id);
        $this->assertSame('4_zones_b', $updatedLayout->type);
        $this->assertSame('My layout', $updatedLayout->name);
        $this->assertSame('My layout description', $updatedLayout->description);
        $this->assertSame(Value::STATUS_DRAFT, $updatedLayout->status);
        $this->assertFalse($updatedLayout->shared);

        $this->assertSame($originalLayout->created, $updatedLayout->created);
        $this->assertGreaterThan($originalLayout->modified, $updatedLayout->modified);

        $this->assertSame(
            [
                'bottom' => [
                    'identifier' => 'bottom',
                    'layoutId' => $updatedLayout->id,
                    'status' => Value::STATUS_DRAFT,
                    'rootBlockId' => 42,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'left' => [
                    'identifier' => 'left',
                    'layoutId' => $updatedLayout->id,
                    'status' => Value::STATUS_DRAFT,
                    'rootBlockId' => 40,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'right' => [
                    'identifier' => 'right',
                    'layoutId' => $updatedLayout->id,
                    'status' => Value::STATUS_DRAFT,
                    'rootBlockId' => 41,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'top' => [
                    'identifier' => 'top',
                    'layoutId' => $updatedLayout->id,
                    'status' => Value::STATUS_DRAFT,
                    'rootBlockId' => 39,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
            ],
            $this->exportObjectList(
                $this->layoutHandler->loadLayoutZones($updatedLayout)
            )
        );

        $this->assertSame(
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

        $this->assertEmpty(
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(40, Value::STATUS_DRAFT)
            )
        );

        $this->assertEmpty(
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(41, Value::STATUS_DRAFT)
            )
        );

        $this->assertEmpty(
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(42, Value::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayoutStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     */
    public function testCreateLayoutStatus(): void
    {
        // Link the zone before copying, to make sure those are copied too
        $this->layoutHandler->updateZone(
            $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'left'),
            new ZoneUpdateStruct(
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

        $this->assertInstanceOf(Layout::class, $copiedLayout);

        $this->assertSame(1, $copiedLayout->id);
        $this->assertSame('4_zones_a', $copiedLayout->type);
        $this->assertSame('My layout', $copiedLayout->name);
        $this->assertSame('My layout description', $copiedLayout->description);
        $this->assertSame(Value::STATUS_ARCHIVED, $copiedLayout->status);
        $this->assertFalse($copiedLayout->shared);
        $this->assertSame('en', $copiedLayout->mainLocale);
        $this->assertSame(['en', 'hr'], $copiedLayout->availableLocales);

        $this->assertSame($originalLayout->created, $copiedLayout->created);
        $this->assertGreaterThan($originalLayout->modified, $copiedLayout->modified);

        $this->assertSame(
            [
                'bottom' => [
                    'identifier' => 'bottom',
                    'layoutId' => 1,
                    'status' => Value::STATUS_ARCHIVED,
                    'rootBlockId' => 4,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'left' => [
                    'identifier' => 'left',
                    'layoutId' => 1,
                    'status' => Value::STATUS_ARCHIVED,
                    'rootBlockId' => 2,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'left',
                ],
                'right' => [
                    'identifier' => 'right',
                    'layoutId' => 1,
                    'status' => Value::STATUS_ARCHIVED,
                    'rootBlockId' => 3,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
                'top' => [
                    'identifier' => 'top',
                    'layoutId' => 1,
                    'status' => Value::STATUS_ARCHIVED,
                    'rootBlockId' => 1,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ],
            ],
            $this->exportObjectList(
                $this->layoutHandler->loadLayoutZones($copiedLayout)
            )
        );

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertCount(2, $archivedReferences);
        $this->assertContains($archivedReferences[0]->collectionId, [2, 3]);
        $this->assertContains($archivedReferences[1]->collectionId, [2, 3]);

        // Second block
        $archivedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(35, Value::STATUS_ARCHIVED)
        );

        $this->assertCount(1, $archivedReferences);
        $this->assertSame(4, $archivedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutZones
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "1"
     */
    public function testDeleteLayout(): void
    {
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutZones
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

        $this->assertCount(2, $publishedReferences);
        $this->assertContains($publishedReferences[0]->collectionId, [2, 3]);
        $this->assertContains($publishedReferences[1]->collectionId, [2, 3]);

        // Second block
        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(35, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(1, $publishedReferences);
        $this->assertSame(4, $publishedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateLayoutModifiedDate
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     */
    public function testDeleteLayoutTranslation(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->deleteLayoutTranslation($layout, 'hr');

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertSame($layout->created, $updatedLayout->created);
        $this->assertGreaterThan($layout->modified, $updatedLayout->modified);

        $this->assertSame('en', $updatedLayout->mainLocale);
        $this->assertSame(['en'], $updatedLayout->availableLocales);

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($updatedLayout);
        foreach ($layoutBlocks as $layoutBlock) {
            $this->assertNotContains('hr', $layoutBlock->availableLocales);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateLayoutModifiedDate
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "31"
     */
    public function testDeleteLayoutTranslationWithInconsistentBlock(): void
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);

        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);

        $block = $this->blockHandler->setMainTranslation($block, 'hr');
        $this->blockHandler->deleteBlockTranslation($block, 'en');

        $updatedLayout = $this->layoutHandler->deleteLayoutTranslation($layout, 'hr');

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertSame($layout->created, $updatedLayout->created);
        $this->assertGreaterThan($layout->modified, $updatedLayout->modified);

        $this->assertSame('en', $updatedLayout->mainLocale);
        $this->assertSame(['en'], $updatedLayout->availableLocales);

        $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Layout does not have the provided locale.
     */
    public function testDeleteLayoutTranslationWithNonExistingLocale(): void
    {
        $this->layoutHandler->deleteLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'de'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Main translation cannot be removed from the layout.
     */
    public function testDeleteLayoutTranslationWithMainLocale(): void
    {
        $this->layoutHandler->deleteLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'en'
        );
    }
}
