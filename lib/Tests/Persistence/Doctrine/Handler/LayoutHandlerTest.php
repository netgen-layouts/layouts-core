<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Exception\NotFoundException;
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
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareHandlers();

        $this->layoutHandler = $this->createLayoutHandler();
        $this->blockHandler = $this->createBlockHandler();
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
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::createLayoutStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZonesData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::loadZoneBlocksData
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
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteLayout()
    {
        // We need to delete the blocks to delete the layout
        $query = $this->databaseConnection->createQueryBuilder();
        $query->delete('ngbm_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', 1);
        $query->execute();

        $this->layoutHandler->deleteLayout(1);

        $this->layoutHandler->loadLayout(1, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteLayoutInOneStatus()
    {
        // We need to delete the blocks to delete the layout
        $query = $this->databaseConnection->createQueryBuilder();
        $query->delete('ngbm_block')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('layout_id', 1)
            ->setParameter('status', APILayout::STATUS_DRAFT);
        $query->execute();

        $this->layoutHandler->deleteLayout(1, APILayout::STATUS_DRAFT);

        // First, verify that NOT all layout statuses are deleted
        try {
            $this->layoutHandler->loadLayout(1, APILayout::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the layout in draft status deleted other/all statuses.');
        }

        $this->layoutHandler->loadLayout(1, APILayout::STATUS_DRAFT);
    }
}
