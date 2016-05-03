<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Block;

class LayoutHandlerTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareHandlers();
    }

    /**
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::__construct
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::applyStatusCondition
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createLayoutSelectQuery
     */
    public function testLoadLayout()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Layout(
                array(
                    'id' => 1,
                    'parentId' => null,
                    'identifier' => '3_zones_a',
                    'name' => 'My layout',
                    'created' => 1447065813,
                    'modified' => 1447065813,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            $handler->loadLayout(1, APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadLayout(999999, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createZoneSelectQuery
     */
    public function testLoadZone()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Zone(
                array(
                    'identifier' => 'top_left',
                    'layoutId' => 1,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            $handler->loadZone(1, 'top_left', APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadZone(999999, 'bottom', APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadZone(1, 'non_existing', APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     */
    public function testLayoutExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(true, $handler->layoutExists(1, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     */
    public function testLayoutNotExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->layoutExists(999999, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::layoutExists
     */
    public function testLayoutNotExistsInStatus()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->layoutExists(1, APILayout::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     */
    public function testZoneExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(true, $handler->zoneExists(1, 'top_left', APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     */
    public function testZoneNotExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->zoneExists(1, 'non_existing', APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::zoneExists
     */
    public function testZoneNotExistsInStatus()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->zoneExists(1, 'top_left', APILayout::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     */
    public function testLoadLayoutZones()
    {
        $handler = $this->createLayoutHandler();

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
            $handler->loadLayoutZones(1, APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::loadLayoutZones
     */
    public function testLoadLayoutZonesForNonExistingLayout()
    {
        $handler = $this->createLayoutHandler();
        self::assertEquals(array(), $handler->loadLayoutZones(999999, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createZoneInsertQuery
     */
    public function testCreateLayout()
    {
        $handler = $this->createLayoutHandler();

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->identifier = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');
        $layoutCreateStruct->status = APILayout::STATUS_PUBLISHED;

        $createdLayout = $handler->createLayout($layoutCreateStruct);

        self::assertInstanceOf(Layout::class, $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertNull($createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->identifier);
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
            $handler->loadLayoutZones($createdLayout->id, $createdLayout->status)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createZoneInsertQuery
     */
    public function testCreateLayoutWithParentLayout()
    {
        $handler = $this->createLayoutHandler();

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->identifier = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');
        $layoutCreateStruct->status = APILayout::STATUS_PUBLISHED;

        $createdLayout = $handler->createLayout($layoutCreateStruct, 1);

        self::assertInstanceOf(Layout::class, $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertEquals(1, $createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->identifier);
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
            $handler->loadLayoutZones($createdLayout->id, $createdLayout->status)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::copyLayout
     */
    public function testCopyLayout()
    {
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::createLayoutStatus
     */
    public function testCreateLayoutStatus()
    {
        $handler = $this->createLayoutHandler();
        $blockHandler = $this->createBlockHandler();
        $copiedLayout = $handler->createLayoutStatus(1, APILayout::STATUS_PUBLISHED, APILayout::STATUS_ARCHIVED);

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(1, $copiedLayout->id);
        self::assertNull($copiedLayout->parentId);
        self::assertEquals('3_zones_a', $copiedLayout->identifier);
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
            $handler->loadLayoutZones(1, APILayout::STATUS_ARCHIVED)
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
            $blockHandler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_ARCHIVED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::updateLayoutStatus
     */
    public function testUpdateLayoutStatus()
    {
        $handler = $this->createLayoutHandler();
        $blockHandler = $this->createBlockHandler();
        $copiedLayout = $handler->updateLayoutStatus(1, APILayout::STATUS_DRAFT, APILayout::STATUS_ARCHIVED);

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(1, $copiedLayout->id);
        self::assertNull($copiedLayout->parentId);
        self::assertEquals('3_zones_a', $copiedLayout->identifier);
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
            $handler->loadLayoutZones(1, APILayout::STATUS_ARCHIVED)
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
            $blockHandler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_ARCHIVED)
        );

        try {
            $handler->loadLayout(1, APILayout::STATUS_DRAFT);
            self::fail('Layout in old status still exists after updating the status');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteLayout()
    {
        $handler = $this->createLayoutHandler();

        // We need to delete the blocks and block items to delete the layout
        $query = $this->databaseConnection->createQueryBuilder();
        $query->delete('ngbm_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', 1);
        $query->execute();

        $handler->deleteLayout(1);

        $handler->loadLayout(1, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Handler\LayoutHandler::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteLayoutInOneStatus()
    {
        $handler = $this->createLayoutHandler();

        // We need to delete the blocks and block items to delete the layout
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

        $handler->deleteLayout(1, APILayout::STATUS_DRAFT);

        // First, verify that NOT all layout statuses are deleted
        try {
            $handler->loadLayout(1, APILayout::STATUS_PUBLISHED);
        } catch (NotFoundException $e) {
            self::fail('Deleting the layout in draft status deleted other/all statuses.');
        }

        $handler->loadLayout(1, APILayout::STATUS_DRAFT);
    }
}
