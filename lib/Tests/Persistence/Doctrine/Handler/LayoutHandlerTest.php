<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\LayoutUpdateStruct;
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
        self::assertEquals(
            new Layout(
                array(
                    'id' => 1,
                    'type' => '4_zones_a',
                    'name' => 'My layout',
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
            $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $this->layoutHandler->loadLayout(999999, Layout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::getZoneSelectQuery
     */
    public function testLoadZone()
    {
        self::assertEquals(
            new Zone(
                array(
                    'identifier' => 'left',
                    'layoutId' => 1,
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
            $this->layoutHandler->loadZone(
                $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED),
                'left'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadZoneData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundException()
    {
        $this->layoutHandler->loadZone(
            $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutExists()
    {
        self::assertTrue($this->layoutHandler->layoutExists(1, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutNotExists()
    {
        self::assertFalse($this->layoutHandler->layoutExists(999999, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutExists
     */
    public function testLayoutNotExistsInStatus()
    {
        self::assertFalse($this->layoutHandler->layoutExists(1, Layout::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::zoneExists
     */
    public function testZoneExists()
    {
        self::assertTrue(
            $this->layoutHandler->zoneExists(
                $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED),
                'left'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::zoneExists
     */
    public function testZoneNotExists()
    {
        self::assertFalse(
            $this->layoutHandler->zoneExists(
                $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED),
                'non_existing'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameExists()
    {
        self::assertTrue($this->layoutHandler->layoutNameExists('My layout', null, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExists()
    {
        self::assertFalse($this->layoutHandler->layoutNameExists('Non existent', null, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExistsWithExcludedId()
    {
        self::assertFalse($this->layoutHandler->layoutNameExists('My layout', 1, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutNameExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::layoutNameExists
     */
    public function testLayoutNameNotExistsInStatus()
    {
        self::assertFalse($this->layoutHandler->layoutNameExists('My layout', null, Layout::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutZonesData
     */
    public function testLoadLayoutZones()
    {
        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'left',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'right',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones(
                $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED)
            )
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

        $createdLayout = $this->layoutHandler->createLayout(
            $layoutCreateStruct,
            Layout::STATUS_DRAFT,
            array('first_zone', 'second_zone')
        );

        self::assertInstanceOf(Layout::class, $createdLayout);

        self::assertEquals(5, $createdLayout->id);
        self::assertEquals('new_layout', $createdLayout->type);
        self::assertEquals('New layout', $createdLayout->name);
        self::assertEquals(Layout::STATUS_DRAFT, $createdLayout->status);

        self::assertInternalType('int', $createdLayout->created);
        self::assertGreaterThan(0, $createdLayout->created);

        self::assertInternalType('int', $createdLayout->modified);
        self::assertGreaterThan(0, $createdLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'first_zone',
                        'layoutId' => $createdLayout->id,
                        'status' => Layout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'second_zone',
                        'layoutId' => $createdLayout->id,
                        'status' => Layout::STATUS_DRAFT,
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

        $originalLayout = $this->layoutHandler->loadLayout(1, Layout::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->updateLayout(
            $originalLayout,
            $layoutUpdateStruct
        );

        self::assertInstanceOf(Layout::class, $updatedLayout);
        self::assertEquals('New name', $updatedLayout->name);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateModified
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::updateModified
     */
    public function testUpdateModified()
    {
        $time = time();

        $originalLayout = $this->layoutHandler->loadLayout(1, Layout::STATUS_DRAFT);
        $updatedLayout = $this->layoutHandler->updateModified(
            $originalLayout,
            $time
        );

        self::assertInstanceOf(Layout::class, $updatedLayout);
        self::assertEquals($originalLayout->created, $updatedLayout->created);
        self::assertEquals($time, $updatedLayout->modified);
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
        $copiedLayoutId = $this->layoutHandler->copyLayout(1);
        $copiedLayout = $this->layoutHandler->loadLayout($copiedLayoutId, Layout::STATUS_PUBLISHED);

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(5, $copiedLayout->id);
        self::assertEquals('4_zones_a', $copiedLayout->type);
        self::assertRegExp('/^My layout \(copy\) \d+$/', $copiedLayout->name);
        self::assertEquals(Layout::STATUS_PUBLISHED, $copiedLayout->status);

        self::assertGreaterThan(0, $copiedLayout->created);
        self::assertGreaterThan(0, $copiedLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => $copiedLayout->id,
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'left',
                        'layoutId' => $copiedLayout->id,
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'right',
                        'layoutId' => $copiedLayout->id,
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top',
                        'layoutId' => $copiedLayout->id,
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones($copiedLayout)
        );

        self::assertEquals(
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
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone(
                    $copiedLayout,
                    'left'
                )
            )
        );

        self::assertEquals(
            array(
                new Block(
                    array(
                        'id' => 8,
                        'layoutId' => $copiedLayout->id,
                        'zoneIdentifier' => 'right',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'parameters' => array(
                            'number_of_columns' => 2,
                        ),
                        'viewType' => 'list',
                        'itemViewType' => 'standard',
                        'name' => 'My block',
                        'status' => Layout::STATUS_PUBLISHED,
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
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone(
                    $copiedLayout,
                    'right'
                )
            )
        );

        // Verify that non named collections were copied
        $this->collectionHandler->loadCollection(4, Collection::STATUS_DRAFT);
        $this->collectionHandler->loadCollection(7, Collection::STATUS_PUBLISHED);

        // Verify the state of the collection references

        // First block
        $draftReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(7, Layout::STATUS_DRAFT)
        );

        self::assertCount(1, $draftReferences);
        self::assertEquals(3, $draftReferences[0]->collectionId);

        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(7, Layout::STATUS_PUBLISHED)
        );

        self::assertCount(1, $draftReferences);
        self::assertEquals(3, $publishedReferences[0]->collectionId);

        // Second block

        $draftReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(8, Layout::STATUS_DRAFT)
        );

        self::assertCount(2, $draftReferences);
        self::assertContains($draftReferences[0]->collectionId, array(3, 6));
        self::assertContains($draftReferences[1]->collectionId, array(3, 6));

        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(8, Layout::STATUS_PUBLISHED)
        );

        self::assertCount(2, $draftReferences);
        self::assertContains($publishedReferences[0]->collectionId, array(3, 7));
        self::assertContains($publishedReferences[1]->collectionId, array(3, 7));
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
        $copiedLayout = $this->layoutHandler->createLayoutStatus(
            $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED),
            Layout::STATUS_ARCHIVED
        );

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(1, $copiedLayout->id);
        self::assertEquals('4_zones_a', $copiedLayout->type);
        self::assertEquals('My layout', $copiedLayout->name);
        self::assertEquals(Layout::STATUS_ARCHIVED, $copiedLayout->status);

        self::assertGreaterThan(0, $copiedLayout->created);
        self::assertGreaterThan(0, $copiedLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_ARCHIVED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'left',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_ARCHIVED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'right',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_ARCHIVED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top',
                        'layoutId' => 1,
                        'status' => Layout::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones($copiedLayout)
        );

        self::assertEquals(
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
                        'status' => Layout::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone($copiedLayout, 'left')
            )
        );

        self::assertEquals(
            array(
                new Block(
                    array(
                        'id' => 1,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'right',
                        'position' => 0,
                        'definitionIdentifier' => 'list',
                        'parameters' => array(
                            'number_of_columns' => 2,
                        ),
                        'viewType' => 'list',
                        'itemViewType' => 'standard',
                        'name' => 'My block',
                        'status' => Layout::STATUS_ARCHIVED,
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
                        'status' => Layout::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone($copiedLayout, 'right')
            )
        );

        // Verify that non named collection status was copied
        $this->collectionHandler->loadCollection(2, Collection::STATUS_ARCHIVED);

        // Verify the state of the collection references
        $archivedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(1, Layout::STATUS_ARCHIVED)
        );
        self::assertCount(2, $archivedReferences);
        self::assertContains($archivedReferences[0]->collectionId, array(2, 3));
        self::assertContains($archivedReferences[1]->collectionId, array(2, 3));

        // Second block
        $archivedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(2, Layout::STATUS_ARCHIVED)
        );
        self::assertCount(1, $archivedReferences);
        self::assertEquals(3, $archivedReferences[0]->collectionId);
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
            $this->collectionHandler->loadCollection(1, Collection::STATUS_DRAFT);
            $this->collectionHandler->loadCollection(2, Collection::STATUS_PUBLISHED);
            self::fail('Collections not deleted after deleting the layout.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that named collection is not deleted (ID == 3)
        $this->collectionHandler->loadCollection(3, Collection::STATUS_PUBLISHED);

        // Verify that we don't have the layout any more
        $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::loadLayoutCollectionsData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayoutBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler::deleteLayout
     */
    public function testDeleteLayoutInOneStatus()
    {
        $this->layoutHandler->deleteLayout(1, Layout::STATUS_DRAFT);

        // Verify that we don't have the layout in deleted status any more
        try {
            $this->layoutHandler->loadLayout(1, Layout::STATUS_DRAFT);
            self::fail('Layout not deleted after deleting it in one status.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that NOT all layout statuses are deleted
        $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED);

        // Verify that we don't have the collection that was related to layout in deleted status any more
        try {
            $this->collectionHandler->loadCollection(1, Collection::STATUS_DRAFT);
            self::fail('Collection not deleted after deleting layout in one status.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that NOT all collections are deleted, especially the named one (ID == 3)
        $this->collectionHandler->loadCollection(2, Collection::STATUS_PUBLISHED);
        $this->collectionHandler->loadCollection(3, Collection::STATUS_PUBLISHED);

        // Verify the state of the collection references
        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(1, Layout::STATUS_PUBLISHED)
        );

        self::assertCount(2, $publishedReferences);
        self::assertContains($publishedReferences[0]->collectionId, array(2, 3));
        self::assertContains($publishedReferences[1]->collectionId, array(2, 3));

        // Second block
        $publishedReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(2, Layout::STATUS_PUBLISHED)
        );

        self::assertCount(1, $publishedReferences);
        self::assertEquals(3, $publishedReferences[0]->collectionId);
    }
}
