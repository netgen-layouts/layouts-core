<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;

class HandlerTest extends \PHPUnit_Framework_TestCase
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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::__construct
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayout
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
            $handler->loadLayout(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadLayout(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     */
    public function testLoadZone()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Zone(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'identifier' => 'top_left',
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            $handler->loadZone(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadZone(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayoutZones
     */
    public function testLoadLayoutZones()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 1,
                        'layoutId' => 1,
                        'identifier' => 'top_left',
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'id' => 2,
                        'layoutId' => 1,
                        'identifier' => 'top_right',
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
                new Zone(
                    array(
                        'id' => 3,
                        'layoutId' => 1,
                        'identifier' => 'bottom',
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $handler->loadLayoutZones(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayoutZones
     */
    public function testLoadLayoutZonesForNonExistingLayout()
    {
        $handler = $this->createLayoutHandler();
        self::assertEquals(array(), $handler->loadLayoutZones(PHP_INT_MAX));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneInsertQuery
     */
    public function testCreateLayout()
    {
        $handler = $this->createLayoutHandler();

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->identifier = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');

        $createdLayout = $handler->createLayout($layoutCreateStruct);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertNull($createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->identifier);
        self::assertEquals('New layout', $createdLayout->name);
        self::assertEquals(0, $createdLayout->status);

        self::assertInternalType('int', $createdLayout->created);
        self::assertGreaterThan(0, $createdLayout->created);

        self::assertInternalType('int', $createdLayout->modified);
        self::assertGreaterThan(0, $createdLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => 3,
                        'identifier' => 'first_zone',
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => 3,
                        'identifier' => 'second_zone',
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
            ),
            $handler->loadLayoutZones($createdLayout->id, $createdLayout->status)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneInsertQuery
     */
    public function testCreateLayoutWithParentLayout()
    {
        $handler = $this->createLayoutHandler();

        $layoutCreateStruct = new LayoutCreateStruct();
        $layoutCreateStruct->identifier = 'new_layout';
        $layoutCreateStruct->name = 'New layout';
        $layoutCreateStruct->zoneIdentifiers = array('first_zone', 'second_zone');

        $createdLayout = $handler->createLayout($layoutCreateStruct, 1);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $createdLayout);

        self::assertEquals(3, $createdLayout->id);
        self::assertEquals(1, $createdLayout->parentId);
        self::assertEquals('new_layout', $createdLayout->identifier);
        self::assertEquals('New layout', $createdLayout->name);
        self::assertEquals(0, $createdLayout->status);

        self::assertInternalType('int', $createdLayout->created);
        self::assertGreaterThan(0, $createdLayout->created);

        self::assertInternalType('int', $createdLayout->modified);
        self::assertGreaterThan(0, $createdLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => 3,
                        'identifier' => 'first_zone',
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => 3,
                        'identifier' => 'second_zone',
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
            ),
            $handler->loadLayoutZones($createdLayout->id, $createdLayout->status)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::copyLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneInsertQuery
     */
    public function testCopyLayout()
    {
        $handler = $this->createLayoutHandler();

        $copiedLayout = $handler->copyLayout(1);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $copiedLayout);

        self::assertEquals(3, $copiedLayout->id);
        self::assertNull($copiedLayout->parentId);
        self::assertEquals('3_zones_a', $copiedLayout->identifier);
        self::assertEquals('My layout', $copiedLayout->name);
        self::assertEquals(0, $copiedLayout->status);

        self::assertInternalType('int', $copiedLayout->created);
        self::assertGreaterThan(0, $copiedLayout->created);

        self::assertInternalType('int', $copiedLayout->modified);
        self::assertGreaterThan(0, $copiedLayout->modified);

        self::assertEquals(
            array(
                new Zone(
                    array(
                        'id' => 7,
                        'layoutId' => 3,
                        'identifier' => 'top_left',
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'id' => 8,
                        'layoutId' => 3,
                        'identifier' => 'top_right',
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'id' => 9,
                        'layoutId' => 3,
                        'identifier' => 'bottom',
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
            ),
            $handler->loadLayoutZones($copiedLayout->id, $copiedLayout->status)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::copyLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneInsertQuery
     */
    public function testCopyLayoutWithDifferentStatuses()
    {
        $handler = $this->createLayoutHandler();

        $copiedLayout = $handler->copyLayout(1, true, APILayout::STATUS_DRAFT, APILayout::STATUS_ARCHIVED);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $copiedLayout);

        self::assertEquals(APILayout::STATUS_ARCHIVED, $copiedLayout->status);

        $zones = $handler->loadLayoutZones($copiedLayout->id, $copiedLayout->status);

        foreach ($zones as $zone) {
            self::assertEquals(APILayout::STATUS_ARCHIVED, $zone->status);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::copyLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneInsertQuery
     */
    public function testCopyLayoutWithSameId()
    {
        $handler = $this->createLayoutHandler();

        $copiedLayout = $handler->copyLayout(1, false, APILayout::STATUS_DRAFT, APILayout::STATUS_ARCHIVED);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $copiedLayout);

        self::assertEquals(1, $copiedLayout->id);

        $zones = $handler->loadLayoutZones($copiedLayout->id, $copiedLayout->status);

        self::assertEquals(1, $zones[0]->id);
        self::assertEquals(1, $zones[0]->layoutId);

        self::assertEquals(2, $zones[1]->id);
        self::assertEquals(1, $zones[1]->layoutId);

        self::assertEquals(3, $zones[2]->id);
        self::assertEquals(1, $zones[2]->layoutId);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::publishLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::updateLayoutStatus
     */
    public function testPublishLayout()
    {
        $handler = $this->createLayoutHandler();

        $publishedLayout = $handler->publishLayout(1);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $publishedLayout);
        self::assertEquals(APILayout::STATUS_PUBLISHED, $publishedLayout->status);

        $archivedLayout = $handler->loadLayout(1, APILayout::STATUS_ARCHIVED);

        self::assertInstanceOf('Netgen\BlockManager\Persistence\Values\Page\Layout', $archivedLayout);

        try {
            $handler->loadLayout(1, APILayout::STATUS_DRAFT);
            self::fail('Draft layout still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteLayout()
    {
        $handler = $this->createLayoutHandler();

        $layoutZones = $handler->loadLayoutZones(1);

        // We need to delete the blocks and block items from zones
        // to be able to delete the zones themselves
        foreach ($layoutZones as $layoutZone) {
            $query = $this->databaseConnection->createQueryBuilder();
            $query->delete('ngbm_block')
                ->where(
                    $query->expr()->eq('zone_id', ':zone_id')
                )
                ->setParameter('zone_id', $layoutZone->id);
            $query->execute();
        }

        $handler->deleteLayout(1);

        $handler->loadLayout(1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::deleteLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteLayoutInDraftStatus()
    {
        $handler = $this->createLayoutHandler();

        $layoutZones = $handler->loadLayoutZones(1, APILayout::STATUS_DRAFT);

        // We need to delete the blocks and block items from zones
        // to be able to delete the zones themselves
        foreach ($layoutZones as $layoutZone) {
            $query = $this->databaseConnection->createQueryBuilder();
            $query->delete('ngbm_block')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('zone_id', ':zone_id'),
                        $query->expr()->eq('status', ':status')
                    )
                )
                ->setParameter('zone_id', $layoutZone->id)
                ->setParameter('status', APILayout::STATUS_DRAFT);
            $query->execute();
        }

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
