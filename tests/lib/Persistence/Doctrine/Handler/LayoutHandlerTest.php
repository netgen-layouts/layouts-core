<?php

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
use PHPUnit\Framework\TestCase;

final class LayoutHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler
     */
    private $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler
     */
    private $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler
     */
    private $collectionHandler;

    public function setUp()
    {
        $this->createDatabase();

        $this->layoutHandler = $this->createLayoutHandler();
        $this->blockHandler = $this->createBlockHandler();
        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
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
    public function testLoadLayout()
    {
        $this->assertEquals(
            new Layout(
                [
                    'id' => 1,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'description' => 'My layout description',
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'status' => Value::STATUS_PUBLISHED,
                    'shared' => false,
                    'mainLocale' => 'en',
                    'availableLocales' => ['en', 'hr'],
                ]
            ),
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "999999"
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $this->layoutHandler->loadLayout(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getZoneSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     */
    public function testLoadZone()
    {
        $this->assertEquals(
            new Zone(
                [
                    'identifier' => 'top',
                    'layoutId' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'rootBlockId' => 5,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'top',
                ]
            ),
            $this->layoutHandler->loadZone(2, Value::STATUS_PUBLISHED, 'top')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find zone with identifier "non_existing"
     */
    public function testLoadZoneThrowsNotFoundException()
    {
        $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadLayouts()
    {
        $this->assertEquals(
            [
                new Layout(
                    [
                        'id' => 1,
                        'type' => '4_zones_a',
                        'name' => 'My layout',
                        'description' => 'My layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en', 'hr'],
                    ]
                ),
                new Layout(
                    [
                        'id' => 2,
                        'type' => '4_zones_b',
                        'name' => 'My other layout',
                        'description' => 'My other layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
                new Layout(
                    [
                        'id' => 6,
                        'type' => '4_zones_b',
                        'name' => 'My sixth layout',
                        'description' => 'My sixth layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
            ],
            $this->layoutHandler->loadLayouts()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadLayoutsWithUnpublishedLayouts()
    {
        $this->assertEquals(
            [
                new Layout(
                    [
                        'id' => 4,
                        'type' => '4_zones_b',
                        'name' => 'My fourth layout',
                        'description' => 'My fourth layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_DRAFT,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
                new Layout(
                    [
                        'id' => 1,
                        'type' => '4_zones_a',
                        'name' => 'My layout',
                        'description' => 'My layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en', 'hr'],
                    ]
                ),
                new Layout(
                    [
                        'id' => 2,
                        'type' => '4_zones_b',
                        'name' => 'My other layout',
                        'description' => 'My other layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
                new Layout(
                    [
                        'id' => 7,
                        'type' => '4_zones_b',
                        'name' => 'My seventh layout',
                        'description' => 'My seventh layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_DRAFT,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
                new Layout(
                    [
                        'id' => 6,
                        'type' => '4_zones_b',
                        'name' => 'My sixth layout',
                        'description' => 'My sixth layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
            ],
            $this->layoutHandler->loadLayouts(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadSharedLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     */
    public function testLoadSharedLayouts()
    {
        $this->assertEquals(
            [
                new Layout(
                    [
                        'id' => 5,
                        'type' => '4_zones_b',
                        'name' => 'My fifth layout',
                        'description' => 'My fifth layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => true,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
                new Layout(
                    [
                        'id' => 3,
                        'type' => '4_zones_b',
                        'name' => 'My third layout',
                        'description' => 'My third layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => true,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
            ],
            $this->layoutHandler->loadSharedLayouts()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadRelatedLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadRelatedLayoutsData
     */
    public function testLoadRelatedLayouts()
    {
        $this->assertEquals(
            [
                new Layout(
                    [
                        'id' => 2,
                        'type' => '4_zones_b',
                        'name' => 'My other layout',
                        'description' => 'My other layout description',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                        'mainLocale' => 'en',
                        'availableLocales' => ['en'],
                    ]
                ),
            ],
            $this->layoutHandler->loadRelatedLayouts(
                $this->layoutHandler->loadLayout(3, Value::STATUS_PUBLISHED)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::getRelatedLayoutsCount
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getRelatedLayoutsCount
     */
    public function testGetRelatedLayoutsCount()
    {
        $count = $this->layoutHandler->getRelatedLayoutsCount(
            $this->layoutHandler->loadLayout(3, Value::STATUS_PUBLISHED)
        );

        $this->assertEquals(1, $count);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutExists()
    {
        $this->assertTrue($this->layoutHandler->layoutExists(1, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutNotExists()
    {
        $this->assertFalse($this->layoutHandler->layoutExists(999999, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutNotExistsInStatus()
    {
        $this->assertFalse($this->layoutHandler->layoutExists(1, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::zoneExists
     */
    public function testZoneExists()
    {
        $this->assertTrue(
            $this->layoutHandler->zoneExists(1, Value::STATUS_PUBLISHED, 'left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::zoneExists
     */
    public function testZoneNotExists()
    {
        $this->assertFalse(
            $this->layoutHandler->zoneExists(1, Value::STATUS_PUBLISHED, 'non_existing')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameExists()
    {
        $this->assertTrue($this->layoutHandler->layoutNameExists('My layout'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExists()
    {
        $this->assertFalse($this->layoutHandler->layoutNameExists('Non existent'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedId()
    {
        $this->assertFalse($this->layoutHandler->layoutNameExists('My layout', 1));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutZonesData
     */
    public function testLoadLayoutZones()
    {
        $this->assertEquals(
            [
                'bottom' => new Zone(
                    [
                        'identifier' => 'bottom',
                        'layoutId' => 2,
                        'status' => Value::STATUS_PUBLISHED,
                        'rootBlockId' => 8,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'left' => new Zone(
                    [
                        'identifier' => 'left',
                        'layoutId' => 2,
                        'status' => Value::STATUS_PUBLISHED,
                        'rootBlockId' => 6,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'right' => new Zone(
                    [
                        'identifier' => 'right',
                        'layoutId' => 2,
                        'status' => Value::STATUS_PUBLISHED,
                        'rootBlockId' => 7,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'top' => new Zone(
                    [
                        'identifier' => 'top',
                        'layoutId' => 2,
                        'status' => Value::STATUS_PUBLISHED,
                        'rootBlockId' => 5,
                        'linkedLayoutId' => 3,
                        'linkedZoneIdentifier' => 'top',
                    ]
                ),
            ],
            $this->layoutHandler->loadLayoutZones(
                $this->layoutHandler->loadLayout(2, Value::STATUS_PUBLISHED)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateZone
     */
    public function testUpdateZone()
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

        $this->assertEquals(
            new Zone(
                [
                    'identifier' => 'top',
                    'layoutId' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'rootBlockId' => 1,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'top',
                ]
            ),
            $updatedZone
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateZone
     */
    public function testUpdateZoneWithResettingLinkedZone()
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

        $this->assertEquals(
            new Zone(
                [
                    'identifier' => 'left',
                    'layoutId' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'rootBlockId' => 2,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                ]
            ),
            $updatedZone
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCreateLayout()
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

        $this->assertEquals(8, $createdLayout->id);
        $this->assertEquals('new_layout', $createdLayout->type);
        $this->assertEquals('New layout', $createdLayout->name);
        $this->assertEquals('New description', $createdLayout->description);
        $this->assertEquals(Value::STATUS_DRAFT, $createdLayout->status);
        $this->assertTrue($createdLayout->shared);
        $this->assertEquals('en', $createdLayout->mainLocale);

        $this->assertInternalType('int', $createdLayout->created);
        $this->assertGreaterThan(0, $createdLayout->created);

        $this->assertInternalType('int', $createdLayout->modified);
        $this->assertGreaterThan(0, $createdLayout->modified);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCreateLayoutTranslation()
    {
        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $layout = $this->layoutHandler->createLayoutTranslation($originalLayout, 'de', 'en');

        $this->assertInstanceOf(Layout::class, $layout);

        $this->assertEquals('en', $layout->mainLocale);
        $this->assertEquals(['en', 'hr', 'de'], $layout->availableLocales);
        $this->assertEquals($originalLayout->created, $layout->created);
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
    public function testCreateLayoutTranslationThrowsBadStateExceptionWithExistingLocale()
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
    public function testCreateLayoutTranslationThrowsBadStateExceptionWithNonExistingSourceLocale()
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
    public function testSetMainTranslation()
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->setMainTranslation($layout, 'hr');

        $this->assertEquals('hr', $updatedLayout->mainLocale);
        $this->assertEquals($layout->created, $updatedLayout->created);
        $this->assertGreaterThan($layout->modified, $updatedLayout->modified);

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($updatedLayout);
        foreach ($layoutBlocks as $layoutBlock) {
            $this->assertEquals('hr', $layoutBlock->mainLocale);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::setMainTranslation
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "mainLocale" has an invalid state. Layout does not have the provided locale.
     */
    public function testSetMainTranslationThrowsBadStateExceptionWithNonExistingLocale()
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $this->layoutHandler->setMainTranslation($layout, 'de');
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createZone
     */
    public function testCreateZone()
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

        $this->assertEquals(1, $createdZone->layoutId);
        $this->assertEquals(Value::STATUS_DRAFT, $createdZone->status);
        $this->assertEquals('new_zone', $createdZone->identifier);
        $this->assertEquals(39, $createdZone->rootBlockId);
        $this->assertEquals(3, $createdZone->linkedLayoutId);
        $this->assertEquals('linked_zone', $createdZone->linkedZoneIdentifier);

        $this->assertEquals(
            new Block(
                [
                    'id' => 39,
                    'layoutId' => $createdZone->layoutId,
                    'depth' => 0,
                    'path' => '/39/',
                    'parentId' => 0,
                    'placeholder' => null,
                    'position' => 0,
                    'definitionIdentifier' => '',
                    'viewType' => '',
                    'itemViewType' => '',
                    'name' => '',
                    'isTranslatable' => false,
                    'mainLocale' => 'en',
                    'alwaysAvailable' => true,
                    'availableLocales' => ['en'],
                    'status' => Value::STATUS_DRAFT,
                    'parameters' => ['en' => []],
                    'config' => [],
                ]
            ),
            $this->blockHandler->loadBlock(39, Value::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateLayout
     */
    public function testUpdateLayout()
    {
        $layoutUpdateStruct = new LayoutUpdateStruct();
        $layoutUpdateStruct->name = 'New name';
        $layoutUpdateStruct->description = 'New description';

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->updateLayout(
            $originalLayout,
            $layoutUpdateStruct
        );

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertEquals('New name', $updatedLayout->name);
        $this->assertEquals('New description', $updatedLayout->description);
        $this->assertEquals($originalLayout->created, $updatedLayout->created);
        $this->assertGreaterThan($originalLayout->modified, $updatedLayout->modified);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateLayout
     */
    public function testUpdateLayoutWithDefaultValues()
    {
        $layoutUpdateStruct = new LayoutUpdateStruct();

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->updateLayout(
            $originalLayout,
            $layoutUpdateStruct
        );

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertEquals('My layout', $updatedLayout->name);
        $this->assertEquals('My layout description', $updatedLayout->description);
        $this->assertEquals($originalLayout->created, $updatedLayout->created);
        $this->assertGreaterThan($originalLayout->modified, $updatedLayout->modified);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::copyLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayoutTranslation
     */
    public function testCopyLayout()
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

        $this->assertEquals(8, $copiedLayout->id);
        $this->assertEquals('4_zones_a', $copiedLayout->type);
        $this->assertEquals('New name', $copiedLayout->name);
        $this->assertEquals('New description', $copiedLayout->description);
        $this->assertEquals(Value::STATUS_PUBLISHED, $copiedLayout->status);
        $this->assertFalse($copiedLayout->shared);
        $this->assertEquals('en', $copiedLayout->mainLocale);
        $this->assertEquals(['en', 'hr'], $copiedLayout->availableLocales);

        $this->assertGreaterThan($originalLayout->created, $copiedLayout->created);
        $this->assertEquals($copiedLayout->created, $copiedLayout->modified);

        $this->assertEquals(
            [
                'bottom' => new Zone(
                    [
                        'identifier' => 'bottom',
                        'layoutId' => $copiedLayout->id,
                        'status' => Value::STATUS_PUBLISHED,
                        'rootBlockId' => 39,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'left' => new Zone(
                    [
                        'identifier' => 'left',
                        'layoutId' => $copiedLayout->id,
                        'status' => Value::STATUS_PUBLISHED,
                        'rootBlockId' => 40,
                        'linkedLayoutId' => 3,
                        'linkedZoneIdentifier' => 'left',
                    ]
                ),
                'right' => new Zone(
                    [
                        'identifier' => 'right',
                        'layoutId' => $copiedLayout->id,
                        'status' => Value::STATUS_PUBLISHED,
                        'rootBlockId' => 42,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'top' => new Zone(
                    [
                        'identifier' => 'top',
                        'layoutId' => $copiedLayout->id,
                        'status' => Value::STATUS_PUBLISHED,
                        'rootBlockId' => 45,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
            ],
            $this->layoutHandler->loadLayoutZones($copiedLayout)
        );

        $this->assertEquals(
            [
                new Block(
                    [
                        'id' => 41,
                        'layoutId' => $copiedLayout->id,
                        'depth' => 1,
                        'path' => '/40/41/',
                        'parentId' => 40,
                        'placeholder' => 'root',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My other block',
                        'isTranslatable' => true,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en', 'hr'],
                        'status' => Value::STATUS_PUBLISHED,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                            'hr' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [
                            'http_cache' => [
                                'use_http_cache' => false,
                            ],
                        ],
                    ]
                ),
            ],
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(40, Value::STATUS_PUBLISHED)
            )
        );

        $this->assertEquals(
            [
                new Block(
                    [
                        'id' => 43,
                        'layoutId' => $copiedLayout->id,
                        'depth' => 1,
                        'path' => '/42/43/',
                        'parentId' => 42,
                        'placeholder' => 'root',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard_with_intro',
                        'name' => 'My published block',
                        'isTranslatable' => true,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en', 'hr'],
                        'status' => Value::STATUS_PUBLISHED,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                            'hr' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [],
                    ]
                ),
                new Block(
                    [
                        'id' => 44,
                        'layoutId' => $copiedLayout->id,
                        'depth' => 1,
                        'path' => '/42/44/',
                        'parentId' => 42,
                        'placeholder' => 'root',
                        'position' => 1,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My fourth block',
                        'isTranslatable' => false,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en'],
                        'status' => Value::STATUS_PUBLISHED,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [],
                    ]
                ),
            ],
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(42, Value::STATUS_PUBLISHED)
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
        $this->assertEquals($references[0]->collectionId, 9);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::changeLayoutType
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteZone
     */
    public function testChangeLayoutType()
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

        $this->assertEquals(1, $updatedLayout->id);
        $this->assertEquals('4_zones_b', $updatedLayout->type);
        $this->assertEquals('My layout', $updatedLayout->name);
        $this->assertEquals('My layout description', $updatedLayout->description);
        $this->assertEquals(Value::STATUS_DRAFT, $updatedLayout->status);
        $this->assertFalse($updatedLayout->shared);

        $this->assertEquals($originalLayout->created, $updatedLayout->created);
        $this->assertGreaterThan($originalLayout->modified, $updatedLayout->modified);

        $this->assertEquals(
            [
                'top' => new Zone(
                    [
                        'identifier' => 'top',
                        'layoutId' => $updatedLayout->id,
                        'status' => Value::STATUS_DRAFT,
                        'rootBlockId' => 39,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'left' => new Zone(
                    [
                        'identifier' => 'left',
                        'layoutId' => $updatedLayout->id,
                        'status' => Value::STATUS_DRAFT,
                        'rootBlockId' => 40,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'right' => new Zone(
                    [
                        'identifier' => 'right',
                        'layoutId' => $updatedLayout->id,
                        'status' => Value::STATUS_DRAFT,
                        'rootBlockId' => 41,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'bottom' => new Zone(
                    [
                        'identifier' => 'bottom',
                        'layoutId' => $updatedLayout->id,
                        'status' => Value::STATUS_DRAFT,
                        'rootBlockId' => 42,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
            ],
            $this->layoutHandler->loadLayoutZones($updatedLayout)
        );

        $this->assertEquals(
            [
                new Block(
                    [
                        'id' => 32,
                        'layoutId' => 1,
                        'depth' => 1,
                        'path' => '/39/32/',
                        'parentId' => 39,
                        'placeholder' => 'root',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My other block',
                        'isTranslatable' => true,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en', 'hr'],
                        'status' => Value::STATUS_DRAFT,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                            'hr' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [
                            'http_cache' => [
                                'use_http_cache' => false,
                            ],
                        ],
                    ]
                ),
                new Block(
                    [
                        'id' => 31,
                        'layoutId' => 1,
                        'depth' => 1,
                        'path' => '/39/31/',
                        'parentId' => 39,
                        'placeholder' => 'root',
                        'position' => 1,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'list',
                        'itemViewType' => 'standard',
                        'name' => 'My block',
                        'isTranslatable' => true,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en', 'hr'],
                        'status' => Value::STATUS_DRAFT,
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
                    ]
                ),
                new Block(
                    [
                        'id' => 35,
                        'layoutId' => 1,
                        'depth' => 1,
                        'path' => '/39/35/',
                        'parentId' => 39,
                        'placeholder' => 'root',
                        'position' => 2,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My fourth block',
                        'isTranslatable' => false,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en'],
                        'status' => Value::STATUS_DRAFT,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [],
                    ]
                ),
            ],
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(39, Value::STATUS_DRAFT)
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
    public function testCreateLayoutStatus()
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

        $this->assertEquals(1, $copiedLayout->id);
        $this->assertEquals('4_zones_a', $copiedLayout->type);
        $this->assertEquals('My layout', $copiedLayout->name);
        $this->assertEquals('My layout description', $copiedLayout->description);
        $this->assertEquals(Value::STATUS_ARCHIVED, $copiedLayout->status);
        $this->assertFalse($copiedLayout->shared);
        $this->assertEquals('en', $copiedLayout->mainLocale);
        $this->assertEquals(['en', 'hr'], $copiedLayout->availableLocales);

        $this->assertEquals($originalLayout->created, $copiedLayout->created);
        $this->assertGreaterThan($originalLayout->modified, $copiedLayout->modified);

        $this->assertEquals(
            [
                'bottom' => new Zone(
                    [
                        'identifier' => 'bottom',
                        'layoutId' => 1,
                        'status' => Value::STATUS_ARCHIVED,
                        'rootBlockId' => 4,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'left' => new Zone(
                    [
                        'identifier' => 'left',
                        'layoutId' => 1,
                        'status' => Value::STATUS_ARCHIVED,
                        'rootBlockId' => 2,
                        'linkedLayoutId' => 3,
                        'linkedZoneIdentifier' => 'left',
                    ]
                ),
                'right' => new Zone(
                    [
                        'identifier' => 'right',
                        'layoutId' => 1,
                        'status' => Value::STATUS_ARCHIVED,
                        'rootBlockId' => 3,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
                'top' => new Zone(
                    [
                        'identifier' => 'top',
                        'layoutId' => 1,
                        'status' => Value::STATUS_ARCHIVED,
                        'rootBlockId' => 1,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    ]
                ),
            ],
            $this->layoutHandler->loadLayoutZones($copiedLayout)
        );

        $this->assertEquals(
            [
                new Block(
                    [
                        'id' => 32,
                        'layoutId' => 1,
                        'depth' => 1,
                        'path' => '/2/32/',
                        'parentId' => 2,
                        'placeholder' => 'root',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My other block',
                        'isTranslatable' => true,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en', 'hr'],
                        'status' => Value::STATUS_ARCHIVED,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                            'hr' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [
                            'http_cache' => [
                                'use_http_cache' => false,
                            ],
                        ],
                    ]
                ),
            ],
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(2, Value::STATUS_ARCHIVED)
            )
        );

        $this->assertEquals(
            [
                new Block(
                    [
                        'id' => 31,
                        'layoutId' => 1,
                        'depth' => 1,
                        'path' => '/3/31/',
                        'parentId' => 3,
                        'placeholder' => 'root',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard_with_intro',
                        'name' => 'My published block',
                        'isTranslatable' => true,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en', 'hr'],
                        'status' => Value::STATUS_ARCHIVED,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                            'hr' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [],
                    ]
                ),
                new Block(
                    [
                        'id' => 35,
                        'layoutId' => 1,
                        'depth' => 1,
                        'path' => '/3/35/',
                        'parentId' => 3,
                        'placeholder' => 'root',
                        'position' => 1,
                        'definitionIdentifier' => 'list',
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My fourth block',
                        'isTranslatable' => false,
                        'mainLocale' => 'en',
                        'alwaysAvailable' => true,
                        'availableLocales' => ['en'],
                        'status' => Value::STATUS_ARCHIVED,
                        'parameters' => [
                            'en' => [
                                'number_of_columns' => 3,
                            ],
                        ],
                        'config' => [],
                    ]
                ),
            ],
            $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock(3, Value::STATUS_ARCHIVED)
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
        $this->assertEquals(4, $archivedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutZones
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find layout with identifier "1"
     */
    public function testDeleteLayout()
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
    public function testDeleteLayoutInOneStatus()
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
        $this->assertEquals(4, $publishedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateLayoutModifiedDate
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     */
    public function testDeleteLayoutTranslation()
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->deleteLayoutTranslation($layout, 'hr');

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertEquals($layout->created, $updatedLayout->created);
        $this->assertGreaterThan($layout->modified, $updatedLayout->modified);

        $this->assertEquals('en', $updatedLayout->mainLocale);
        $this->assertEquals(['en'], $updatedLayout->availableLocales);

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
    public function testDeleteLayoutTranslationWithInconsistentBlock()
    {
        $layout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);

        $block = $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);

        $block = $this->blockHandler->setMainTranslation($block, 'hr');
        $this->blockHandler->deleteBlockTranslation($block, 'en');

        $updatedLayout = $this->layoutHandler->deleteLayoutTranslation($layout, 'hr');

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertEquals($layout->created, $updatedLayout->created);
        $this->assertGreaterThan($layout->modified, $updatedLayout->modified);

        $this->assertEquals('en', $updatedLayout->mainLocale);
        $this->assertEquals(['en'], $updatedLayout->availableLocales);

        $this->blockHandler->loadBlock(31, Value::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayoutTranslation
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutTranslations
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "locale" has an invalid state. Layout does not have the provided locale.
     */
    public function testDeleteLayoutTranslationWithNonExistingLocale()
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
    public function testDeleteLayoutTranslationWithMainLocale()
    {
        $this->layoutHandler->deleteLayoutTranslation(
            $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT),
            'en'
        );
    }
}
