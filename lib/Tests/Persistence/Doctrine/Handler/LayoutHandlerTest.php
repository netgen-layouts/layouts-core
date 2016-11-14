<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\ZoneUpdateStruct;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Persistence\Values\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use PHPUnit\Framework\TestCase;

class LayoutHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler
     */
    protected $layoutHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareHandlers();

        $this->layoutHandler = $this->createLayoutHandler();
        $this->blockHandler = $this->createBlockHandler();
        $this->collectionHandler = $this->createCollectionHandler();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     */
    public function testLoadLayout()
    {
        $this->assertEquals(
            new Layout(
                array(
                    'id' => 1,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'status' => Value::STATUS_PUBLISHED,
                    'shared' => false,
                )
            ),
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $this->layoutHandler->loadLayout(999999, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getZoneSelectQuery
     */
    public function testLoadZone()
    {
        $this->assertEquals(
            new Zone(
                array(
                    'identifier' => 'top',
                    'layoutId' => 2,
                    'status' => Value::STATUS_PUBLISHED,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'top',
                )
            ),
            $this->layoutHandler->loadZone(2, Value::STATUS_PUBLISHED, 'top')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundException()
    {
        $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     */
    public function testLoadLayouts()
    {
        $this->assertEquals(
            array(
                new Layout(
                    array(
                        'id' => 1,
                        'type' => '4_zones_a',
                        'name' => 'My layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                    )
                ),
                new Layout(
                    array(
                        'id' => 2,
                        'type' => '4_zones_b',
                        'name' => 'My other layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                    )
                ),
                new Layout(
                    array(
                        'id' => 6,
                        'type' => '4_zones_b',
                        'name' => 'My sixth layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                    )
                ),
            ),
            $this->layoutHandler->loadLayouts()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     */
    public function testLoadLayoutsWithUnpublishedLayouts()
    {
        $this->assertEquals(
            array(
                new Layout(
                    array(
                        'id' => 1,
                        'type' => '4_zones_a',
                        'name' => 'My layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                    )
                ),
                new Layout(
                    array(
                        'id' => 2,
                        'type' => '4_zones_b',
                        'name' => 'My other layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                    )
                ),
                new Layout(
                    array(
                        'id' => 4,
                        'type' => '4_zones_b',
                        'name' => 'My fourth layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_DRAFT,
                        'shared' => false,
                    )
                ),
                new Layout(
                    array(
                        'id' => 6,
                        'type' => '4_zones_b',
                        'name' => 'My sixth layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => false,
                    )
                ),
            ),
            $this->layoutHandler->loadLayouts(true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadSharedLayouts
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getLayoutSelectQuery
     */
    public function testLoadSharedLayouts()
    {
        $this->assertEquals(
            array(
                new Layout(
                    array(
                        'id' => 3,
                        'type' => '4_zones_b',
                        'name' => 'My third layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => true,
                    )
                ),
                new Layout(
                    array(
                        'id' => 5,
                        'type' => '4_zones_b',
                        'name' => 'My fifth layout',
                        'created' => 1447065813,
                        'modified' => 1447065813,
                        'status' => Value::STATUS_PUBLISHED,
                        'shared' => true,
                    )
                ),
            ),
            $this->layoutHandler->loadSharedLayouts()
        );
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
        $this->assertTrue($this->layoutHandler->layoutNameExists('My layout', null, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExists()
    {
        $this->assertFalse($this->layoutHandler->layoutNameExists('Non existent', null, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedId()
    {
        $this->assertFalse($this->layoutHandler->layoutNameExists('My layout', 1, Value::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExistsInStatus()
    {
        $this->assertFalse($this->layoutHandler->layoutNameExists('My layout', null, Value::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutZonesData
     */
    public function testLoadLayoutZones()
    {
        $this->assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => 2,
                        'status' => Value::STATUS_PUBLISHED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'left',
                        'layoutId' => 2,
                        'status' => Value::STATUS_PUBLISHED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'right',
                        'layoutId' => 2,
                        'status' => Value::STATUS_PUBLISHED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top',
                        'layoutId' => 2,
                        'status' => Value::STATUS_PUBLISHED,
                        'linkedLayoutId' => 3,
                        'linkedZoneIdentifier' => 'top',
                    )
                ),
            ),
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
                array(
                    'linkedZone' => $linkedZone,
                )
            )
        );

        $this->assertEquals(
            new Zone(
                array(
                    'identifier' => 'top',
                    'layoutId' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'linkedLayoutId' => 3,
                    'linkedZoneIdentifier' => 'top',
                )
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
                array(
                    'linkedZone' => false,
                )
            )
        );

        $this->assertEquals(
            new Zone(
                array(
                    'identifier' => 'left',
                    'layoutId' => 1,
                    'status' => Value::STATUS_DRAFT,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                )
            ),
            $updatedZone
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     */
    public function testCreateLayout()
    {
        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->type = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->shared = true;
        $layoutCreateStruct->status = Value::STATUS_DRAFT;
        $layoutCreateStruct->zoneCreateStructs = array(
            new ZoneCreateStruct(array('identifier' => 'first_zone')),
            new ZoneCreateStruct(array('identifier' => 'second_zone')),
        );

        $createdLayout = $this->layoutHandler->createLayout($layoutCreateStruct);

        $this->assertInstanceOf(Layout::class, $createdLayout);

        $this->assertEquals(7, $createdLayout->id);
        $this->assertEquals('new_layout', $createdLayout->type);
        $this->assertEquals('New layout', $createdLayout->name);
        $this->assertEquals(Value::STATUS_DRAFT, $createdLayout->status);
        $this->assertTrue($createdLayout->shared);

        $this->assertInternalType('int', $createdLayout->created);
        $this->assertGreaterThan(0, $createdLayout->created);

        $this->assertInternalType('int', $createdLayout->modified);
        $this->assertGreaterThan(0, $createdLayout->modified);

        $this->assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'first_zone',
                        'layoutId' => $createdLayout->id,
                        'status' => Value::STATUS_DRAFT,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'second_zone',
                        'layoutId' => $createdLayout->id,
                        'status' => Value::STATUS_DRAFT,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones($createdLayout)
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
        $layoutUpdateStruct->modified = 123;

        $originalLayout = $this->layoutHandler->loadLayout(1, Value::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->updateLayout(
            $originalLayout,
            $layoutUpdateStruct
        );

        $this->assertInstanceOf(Layout::class, $updatedLayout);
        $this->assertEquals('New name', $updatedLayout->name);
        $this->assertEquals(123, $updatedLayout->modified);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::copyLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutZonesData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadZoneBlocksData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadCollectionReferencesData
     */
    public function testCopyLayout()
    {
        // Link the zone before copying, to make sure those are copied too
        $this->layoutHandler->updateZone(
            $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'left'),
            new ZoneUpdateStruct(
                array(
                    'linkedZone' => $this->layoutHandler->loadZone(3, Value::STATUS_PUBLISHED, 'left'),
                )
            )
        );

        $copiedLayout = $this->layoutHandler->copyLayout(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED),
            'New name'
        );

        $this->assertInstanceOf(Layout::class, $copiedLayout);

        $this->assertEquals(7, $copiedLayout->id);
        $this->assertEquals('4_zones_a', $copiedLayout->type);
        $this->assertEquals('New name', $copiedLayout->name);
        $this->assertEquals(Value::STATUS_PUBLISHED, $copiedLayout->status);
        $this->assertFalse($copiedLayout->shared);

        $this->assertGreaterThan(0, $copiedLayout->created);
        $this->assertGreaterThan(0, $copiedLayout->modified);

        $this->assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => $copiedLayout->id,
                        'status' => Value::STATUS_PUBLISHED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'left',
                        'layoutId' => $copiedLayout->id,
                        'status' => Value::STATUS_PUBLISHED,
                        'linkedLayoutId' => 3,
                        'linkedZoneIdentifier' => 'left',
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'right',
                        'layoutId' => $copiedLayout->id,
                        'status' => Value::STATUS_PUBLISHED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top',
                        'layoutId' => $copiedLayout->id,
                        'status' => Value::STATUS_PUBLISHED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones($copiedLayout)
        );

        $this->assertEquals(
            array(
                new Block(
                    array(
                        'id' => 7,
                        'layoutId' => $copiedLayout->id,
                        'zoneIdentifier' => 'left',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'parameters' => array(
                            'number_of_columns' => 3,
                        ),
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My other block',
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone(
                    $copiedLayout->id,
                    $copiedLayout->status,
                    'left'
                )
            )
        );

        $this->assertEquals(
            array(
                new Block(
                    array(
                        'id' => 8,
                        'layoutId' => $copiedLayout->id,
                        'zoneIdentifier' => 'right',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'parameters' => array(
                            'number_of_columns' => 3,
                        ),
                        'viewType' => 'grid',
                        'itemViewType' => 'standard_with_intro',
                        'name' => 'My published block',
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
                new Block(
                    array(
                        'id' => 9,
                        'layoutId' => $copiedLayout->id,
                        'zoneIdentifier' => 'right',
                        'position' => 1,
                        'definitionIdentifier' => 'list',
                        'parameters' => array(
                            'number_of_columns' => 3,
                        ),
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My fourth block',
                        'status' => Value::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone(
                    $copiedLayout->id,
                    $copiedLayout->status,
                    'right'
                )
            )
        );

        // Verify that non shared collections were copied
        $this->collectionHandler->loadCollection(6, Value::STATUS_PUBLISHED);
        $this->collectionHandler->loadCollection(7, Value::STATUS_PUBLISHED);

        // Verify the state of the collection references

        // First block
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(7, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(1, $references);
        $this->assertEquals($references[0]->collectionId, 3);

        // Second block
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(8, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(2, $references);
        $this->assertContains($references[0]->collectionId, array(3, 6));
        $this->assertContains($references[1]->collectionId, array(3, 6));

        // Second block
        $references = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(9, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(1, $references);
        $this->assertEquals($references[0]->collectionId, 7);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayoutStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutZonesData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadZoneBlocksData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadCollectionReferencesData
     */
    public function testCreateLayoutStatus()
    {
        // Link the zone before copying, to make sure those are copied too
        $this->layoutHandler->updateZone(
            $this->layoutHandler->loadZone(1, Value::STATUS_PUBLISHED, 'left'),
            new ZoneUpdateStruct(
                array(
                    'linkedZone' => $this->layoutHandler->loadZone(3, Value::STATUS_PUBLISHED, 'left'),
                )
            )
        );

        $copiedLayout = $this->layoutHandler->createLayoutStatus(
            $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED),
            Value::STATUS_ARCHIVED
        );

        $this->assertInstanceOf(Layout::class, $copiedLayout);

        $this->assertEquals(1, $copiedLayout->id);
        $this->assertEquals('4_zones_a', $copiedLayout->type);
        $this->assertEquals('My layout', $copiedLayout->name);
        $this->assertEquals(Value::STATUS_ARCHIVED, $copiedLayout->status);
        $this->assertFalse($copiedLayout->shared);

        $this->assertGreaterThan(0, $copiedLayout->created);
        $this->assertGreaterThan(0, $copiedLayout->modified);

        $this->assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => 1,
                        'status' => Value::STATUS_ARCHIVED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'left',
                        'layoutId' => 1,
                        'status' => Value::STATUS_ARCHIVED,
                        'linkedLayoutId' => 3,
                        'linkedZoneIdentifier' => 'left',
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'right',
                        'layoutId' => 1,
                        'status' => Value::STATUS_ARCHIVED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top',
                        'layoutId' => 1,
                        'status' => Value::STATUS_ARCHIVED,
                        'linkedLayoutId' => null,
                        'linkedZoneIdentifier' => null,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones($copiedLayout)
        );

        $this->assertEquals(
            array(
                new Block(
                    array(
                        'id' => 2,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'left',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'parameters' => array(
                            'number_of_columns' => 3,
                        ),
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My other block',
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone($copiedLayout->id, $copiedLayout->status, 'left')
            )
        );

        $this->assertEquals(
            array(
                new Block(
                    array(
                        'id' => 1,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'right',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'parameters' => array(
                            'number_of_columns' => 3,
                        ),
                        'viewType' => 'grid',
                        'itemViewType' => 'standard_with_intro',
                        'name' => 'My published block',
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
                new Block(
                    array(
                        'id' => 5,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'right',
                        'position' => 1,
                        'definitionIdentifier' => 'list',
                        'parameters' => array(
                            'number_of_columns' => 3,
                        ),
                        'viewType' => 'grid',
                        'itemViewType' => 'standard',
                        'name' => 'My fourth block',
                        'status' => Value::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone($copiedLayout->id, $copiedLayout->status, 'right')
            )
        );

        // Verify that non shared collection status was copied
        $this->collectionHandler->loadCollection(2, Value::STATUS_ARCHIVED);

        // Verify the state of the collection references
        $archivedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(1, Value::STATUS_ARCHIVED)
        );
        $this->assertCount(2, $archivedReferences);
        $this->assertContains($archivedReferences[0]->collectionId, array(2, 3));
        $this->assertContains($archivedReferences[1]->collectionId, array(2, 3));

        // Second block
        $archivedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(2, Value::STATUS_ARCHIVED)
        );
        $this->assertCount(1, $archivedReferences);
        $this->assertEquals(3, $archivedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutCollectionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayout
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteLayout()
    {
        $this->layoutHandler->deleteLayout(1);

        // Verify that we don't have the collections that were related to the layout
        try {
            $this->collectionHandler->loadCollection(1, Value::STATUS_DRAFT);
            $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
            self::fail('Collections not deleted after deleting the layout.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that shared collection is not deleted (ID == 3)
        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);

        // Verify that we don't have the layout any more
        $this->layoutHandler->loadLayout(1, Value::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutCollectionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayout
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

        // Verify that NOT all collections are deleted, especially the shared one (ID == 3)
        $this->collectionHandler->loadCollection(2, Value::STATUS_PUBLISHED);
        $this->collectionHandler->loadCollection(3, Value::STATUS_PUBLISHED);

        // Verify the state of the collection references
        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(1, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(2, $publishedReferences);
        $this->assertContains($publishedReferences[0]->collectionId, array(2, 3));
        $this->assertContains($publishedReferences[1]->collectionId, array(2, 3));

        // Second block
        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(2, Value::STATUS_PUBLISHED)
        );

        $this->assertCount(1, $publishedReferences);
        $this->assertEquals(3, $publishedReferences[0]->collectionId);
    }
}
