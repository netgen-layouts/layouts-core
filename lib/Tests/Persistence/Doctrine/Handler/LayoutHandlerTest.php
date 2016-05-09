<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Block;

class LayoutHandlerTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getLayoutSelectQuery
     */
    public function testLoadLayout()
    {
        self::assertEquals(
            new Layout(
                array(
                    'id' => 1,
                    'parentId' => null,
                    'type' => '3_zones_a',
                    'name' => 'My layout',
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            $this->layoutHandler->loadLayout(1, APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutData
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $this->layoutHandler->loadLayout(999999, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getZoneSelectQuery
     */
    public function testLoadZone()
    {
        self::assertEquals(
            new Zone(
                array(
                    'identifier' => 'top_left',
                    'layoutId' => 1,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            $this->layoutHandler->loadZone(1, 'top_left', APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $this->layoutHandler->loadZone(999999, 'bottom', APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone()
    {
        $this->layoutHandler->loadZone(1, 'non_existing', APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     */
    public function testLayoutExists()
    {
        self::assertTrue($this->layoutHandler->layoutExists(1, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     */
    public function testLayoutNotExists()
    {
        self::assertFalse($this->layoutHandler->layoutExists(999999, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     */
    public function testLayoutNotExistsInStatus()
    {
        self::assertFalse($this->layoutHandler->layoutExists(1, APILayout::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     */
    public function testZoneExists()
    {
        self::assertTrue($this->layoutHandler->zoneExists(1, 'top_left', APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     */
    public function testZoneNotExists()
    {
        self::assertFalse($this->layoutHandler->zoneExists(1, 'non_existing', APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     */
    public function testZoneNotExistsInStatus()
    {
        self::assertFalse($this->layoutHandler->zoneExists(1, 'top_left', APILayout::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZonesData
     */
    public function testLoadLayoutZones()
    {
        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_left',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_right',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones(1, APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZonesData
     */
    public function testLoadLayoutZonesForNonExistingLayout()
    {
        self::assertEquals(array(), $this->layoutHandler->loadLayoutZones(999999, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getLayoutInsertQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getZoneInsertQuery
     */
    public function testCreateLayout()
    {
        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->type = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');
        $layoutCreateStruct->status = APILayout::STATUS_PUBLISHED;

        $createdLayout = $this->layoutHandler->createLayout($layoutCreateStruct);

        self::assertInstanceOf(Layout::class, $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertNull($createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->type);
        self::assertEquals('New layout', $createdLayout->name);
        self::assertEquals(APILayout::STATUS_PUBLISHED, $createdLayout->status);

        self::assertInternalType('int', $createdLayout->created);
        self::assertGreaterThan(0, $createdLayout->created);

        self::assertInternalType('int', $createdLayout->modified);
        self::assertGreaterThan(0, $createdLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'first_zone',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'second_zone',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones($createdLayout->id, $createdLayout->status)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getLayoutInsertQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getZoneInsertQuery
     */
    public function testCreateLayoutWithParentLayout()
    {
        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->type = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');
        $layoutCreateStruct->status = APILayout::STATUS_PUBLISHED;

        $createdLayout = $this->layoutHandler->createLayout($layoutCreateStruct, 1);

        self::assertInstanceOf(Layout::class, $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertEquals(1, $createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->type);
        self::assertEquals('New layout', $createdLayout->name);
        self::assertEquals(APILayout::STATUS_PUBLISHED, $createdLayout->status);

        self::assertInternalType('int', $createdLayout->created);
        self::assertGreaterThan(0, $createdLayout->created);

        self::assertInternalType('int', $createdLayout->modified);
        self::assertGreaterThan(0, $createdLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'first_zone',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'second_zone',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones($createdLayout->id, $createdLayout->status)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::copyLayout
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZonesData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZoneBlocksData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadBlockCollectionsData
     */
    public function testCopyLayout()
    {
        $copiedLayout = $this->layoutHandler->copyLayout(1);

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(3, $copiedLayout->id);
        self::assertNull($copiedLayout->parentId);
        self::assertEquals('3_zones_a', $copiedLayout->type);
        self::assertRegExp('/^My layout \(copy\) \d+$/', $copiedLayout->name);
        self::assertEquals(APILayout::STATUS_PUBLISHED, $copiedLayout->status);

        self::assertGreaterThan(0, $copiedLayout->created);
        self::assertGreaterThan(0, $copiedLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => $copiedLayout->id,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_left',
                        'layoutId' => $copiedLayout->id,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_right',
                        'layoutId' => $copiedLayout->id,
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones($copiedLayout->id, APILayout::STATUS_PUBLISHED)
        );

        self::assertEquals(
            array(
                new Block(
                    array(
                        'id' => 5,
                        'layoutId' => $copiedLayout->id,
                        'zoneIdentifier' => 'top_right',
                        'position' => 0,
                        'definitionIdentifier' => 'paragraph',
                        'parameters' => array(
                            'some_param' => 'some_value',
                        ),
                        'viewType' => 'default',
                        'name' => 'My block',
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Block(
                    array(
                        'id' => 6,
                        'layoutId' => $copiedLayout->id,
                        'zoneIdentifier' => 'top_right',
                        'position' => 1,
                        'definitionIdentifier' => 'title',
                        'parameters' => array(
                            'other_param' => 'other_value',
                        ),
                        'viewType' => 'small',
                        'name' => 'My other block',
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks($copiedLayout->id, 'top_right', APILayout::STATUS_PUBLISHED)
        );

        // Verify that non named collections were copied
        $this->collectionHandler->loadCollection(4, Collection::STATUS_PUBLISHED);
        $this->collectionHandler->loadCollection(5, Collection::STATUS_PUBLISHED);

        // Verify the state of the collection references
        $draftReferences = $this->blockHandler->loadBlockCollections(5, APILayout::STATUS_DRAFT);
        self::assertCount(2, $draftReferences);
        self::assertEquals(3, $draftReferences[0]->collectionId);
        self::assertEquals(4, $draftReferences[1]->collectionId);

        $publishedReferences = $this->blockHandler->loadBlockCollections(5, APILayout::STATUS_PUBLISHED);
        self::assertCount(2, $draftReferences);
        self::assertEquals(3, $publishedReferences[0]->collectionId);
        self::assertEquals(5, $publishedReferences[1]->collectionId);

        // Second block
        $draftReferences = $this->blockHandler->loadBlockCollections(6, APILayout::STATUS_DRAFT);
        self::assertCount(1, $draftReferences);
        self::assertEquals(3, $draftReferences[0]->collectionId);

        $publishedReferences = $this->blockHandler->loadBlockCollections(6, APILayout::STATUS_PUBLISHED);
        self::assertCount(1, $draftReferences);
        self::assertEquals(3, $publishedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayoutStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZonesData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZoneBlocksData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadBlockCollectionsData
     */
    public function testCreateLayoutStatus()
    {
        $copiedLayout = $this->layoutHandler->createLayoutStatus(1, APILayout::STATUS_PUBLISHED, APILayout::STATUS_ARCHIVED);

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(1, $copiedLayout->id);
        self::assertNull($copiedLayout->parentId);
        self::assertEquals('3_zones_a', $copiedLayout->type);
        self::assertEquals('My layout', $copiedLayout->name);
        self::assertEquals(APILayout::STATUS_ARCHIVED, $copiedLayout->status);

        self::assertGreaterThan(0, $copiedLayout->created);
        self::assertGreaterThan(0, $copiedLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_left',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_right',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones(1, APILayout::STATUS_ARCHIVED)
        );

        self::assertEquals(
            array(
                new Block(
                    array(
                        'id' => 1,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'top_right',
                        'position' => 0,
                        'definitionIdentifier' => 'paragraph',
                        'parameters' => array(
                            'some_param' => 'some_value',
                        ),
                        'viewType' => 'default',
                        'name' => 'My block',
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
                new Block(
                    array(
                        'id' => 2,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'top_right',
                        'position' => 1,
                        'definitionIdentifier' => 'title',
                        'parameters' => array(
                            'other_param' => 'other_value',
                        ),
                        'viewType' => 'small',
                        'name' => 'My other block',
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_ARCHIVED)
        );

        // Verify that non named collection was copied
        $this->collectionHandler->loadCollection(4, Collection::STATUS_PUBLISHED);

        // Verify the state of the collection references
        $archivedReferences = $this->blockHandler->loadBlockCollections(1, APILayout::STATUS_ARCHIVED);
        self::assertCount(2, $archivedReferences);
        self::assertEquals(3, $archivedReferences[0]->collectionId);
        self::assertEquals(4, $archivedReferences[1]->collectionId);

        // Second block
        $archivedReferences = $this->blockHandler->loadBlockCollections(2, APILayout::STATUS_ARCHIVED);
        self::assertCount(1, $archivedReferences);
        self::assertEquals(3, $archivedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::updateLayoutStatus
     */
    public function testUpdateLayoutStatus()
    {
        $copiedLayout = $this->layoutHandler->updateLayoutStatus(1, APILayout::STATUS_DRAFT, APILayout::STATUS_ARCHIVED);

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(1, $copiedLayout->id);
        self::assertNull($copiedLayout->parentId);
        self::assertEquals('3_zones_a', $copiedLayout->type);
        self::assertEquals('My layout', $copiedLayout->name);
        self::assertEquals(APILayout::STATUS_ARCHIVED, $copiedLayout->status);

        self::assertGreaterThan(0, $copiedLayout->created);
        self::assertGreaterThan(0, $copiedLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'identifier' => 'bottom',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_left',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_right',
                        'layoutId' => 1,
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->layoutHandler->loadLayoutZones(1, APILayout::STATUS_ARCHIVED)
        );

        self::assertEquals(
            array(
                new Block(
                    array(
                        'id' => 1,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'top_right',
                        'position' => 0,
                        'definitionIdentifier' => 'paragraph',
                        'parameters' => array(
                            'some_param' => 'some_value',
                        ),
                        'viewType' => 'default',
                        'name' => 'My block',
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
                new Block(
                    array(
                        'id' => 2,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'top_right',
                        'position' => 1,
                        'definitionIdentifier' => 'title',
                        'parameters' => array(
                            'other_param' => 'other_value',
                        ),
                        'viewType' => 'small',
                        'name' => 'My other block',
                        'status' => APILayout::STATUS_ARCHIVED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_ARCHIVED)
        );

        try {
            $this->layoutHandler->loadLayout(1, APILayout::STATUS_DRAFT);
            self::fail('Layout in old status still exists after updating the status');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify the state of the collection references
        $draftReferences = $this->blockHandler->loadBlockCollections(1, APILayout::STATUS_DRAFT);
        self::assertEmpty($draftReferences);

        $archivedReferences = $this->blockHandler->loadBlockCollections(1, APILayout::STATUS_ARCHIVED);
        self::assertCount(2, $archivedReferences);
        self::assertEquals(1, $archivedReferences[0]->collectionId);
        self::assertEquals(3, $archivedReferences[1]->collectionId);

        // Second block
        $draftReferences = $this->blockHandler->loadBlockCollections(2, APILayout::STATUS_DRAFT);
        self::assertEmpty($draftReferences);

        $archivedReferences = $this->blockHandler->loadBlockCollections(2, APILayout::STATUS_ARCHIVED);
        self::assertCount(1, $archivedReferences);
        self::assertEquals(3, $archivedReferences[0]->collectionId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     */
    public function testDeleteLayout()
    {
        $this->layoutHandler->deleteLayout(1);

        // Verify that we don't have the layout any more
        try {
            $this->layoutHandler->loadLayout(1, APILayout::STATUS_PUBLISHED);
            self::fail('Layout not removed after deleting it.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that we don't have the collections that were related to the layout
        try {
            $this->collectionHandler->loadCollection(1, Collection::STATUS_PUBLISHED);
            $this->collectionHandler->loadCollection(2, Collection::STATUS_PUBLISHED);
            self::fail('Collections not deleted after deleting the layout.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that NOT all collections are deleted, especially the named one (ID == 3)
        $this->collectionHandler->loadCollection(3, Collection::STATUS_PUBLISHED);

        // Verify the state of the collection references
        $draftReferences = $this->blockHandler->loadBlockCollections(1, APILayout::STATUS_DRAFT);
        self::assertEmpty($draftReferences);

        $publishedReferences = $this->blockHandler->loadBlockCollections(1, APILayout::STATUS_PUBLISHED);
        self::assertEmpty($publishedReferences);

        // Second block
        $draftReferences = $this->blockHandler->loadBlockCollections(2, APILayout::STATUS_DRAFT);
        self::assertEmpty($draftReferences);

        $publishedReferences = $this->blockHandler->loadBlockCollections(2, APILayout::STATUS_PUBLISHED);
        self::assertEmpty($publishedReferences);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     */
    public function testDeleteLayoutInOneStatus()
    {
        $this->layoutHandler->deleteLayout(1, APILayout::STATUS_DRAFT);

        // Verify that we don't have the layout in deleted status any more
        try {
            $this->layoutHandler->loadLayout(1, APILayout::STATUS_DRAFT);
            self::fail('Layout not deleted after deleting it in one status.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that NOT all layout statuses are deleted
        $this->layoutHandler->loadLayout(1, APILayout::STATUS_PUBLISHED);

        // Verify that we don't have the collection that was related to layout in deleted status any more
        try {
            $this->collectionHandler->loadCollection(1, Collection::STATUS_PUBLISHED);
            self::fail('Collection not deleted after deleting layout in one status.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that NOT all collections are deleted, especially the named one (ID == 3)
        $this->collectionHandler->loadCollection(2, Collection::STATUS_PUBLISHED);
        $this->collectionHandler->loadCollection(3, Collection::STATUS_PUBLISHED);

        // Verify the state of the collection references
        $draftReferences = $this->blockHandler->loadBlockCollections(1, APILayout::STATUS_DRAFT);
        self::assertEmpty($draftReferences);

        $publishedReferences = $this->blockHandler->loadBlockCollections(1, APILayout::STATUS_PUBLISHED);
        self::assertCount(2, $publishedReferences);
        self::assertEquals(2, $publishedReferences[0]->collectionId);
        self::assertEquals(3, $publishedReferences[1]->collectionId);

        // Second block
        $draftReferences = $this->blockHandler->loadBlockCollections(2, APILayout::STATUS_DRAFT);
        self::assertEmpty($draftReferences);

        $publishedReferences = $this->blockHandler->loadBlockCollections(2, APILayout::STATUS_PUBLISHED);
        self::assertCount(1, $publishedReferences);
        self::assertEquals(3, $publishedReferences[0]->collectionId);
    }
}
