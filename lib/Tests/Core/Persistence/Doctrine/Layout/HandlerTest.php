<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Block;

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
                    'identifier' => 'top_left',
                    'layoutId' => 1,
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            $handler->loadZone(1, 'top_left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadZone(PHP_INT_MAX, 'bottom');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadZone(1, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::zoneExists
     */
    public function testZoneExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(true, $handler->zoneExists(1, 'top_left'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::zoneExists
     */
    public function testZoneNotExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->zoneExists(1, 'non_existing'));
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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::__construct
     */
    public function testLoadBlock()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_PUBLISHED,
                )
            ),
            $handler->loadBlock(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadBlock(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadZoneBlocks
     */
    public function testLoadZoneBlocks()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            array(
                new Block(
                    array(
                        'id' => 1,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'top_right',
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
                        'id' => 2,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'top_right',
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
            $handler->loadZoneBlocks(1, 'top_right')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadZoneBlocks
     */
    public function testLoadZoneBlocksForNonExistingZone()
    {
        $handler = $this->createLayoutHandler();
        self::assertEquals(array(), $handler->loadZoneBlocks(1, 'non_existing'));
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

        self::assertInstanceOf(Layout::class, $createdLayout);

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
                        'identifier' => 'first_zone',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'second_zone',
                        'layoutId' => 3,
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

        self::assertInstanceOf(Layout::class, $createdLayout);

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
                        'identifier' => 'first_zone',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'second_zone',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
            ),
            $handler->loadLayoutZones($createdLayout->id, $createdLayout->status)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockInsertQuery
     */
    public function testCreateBlock()
    {
        $handler = $this->createLayoutHandler();

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'bottom',
                    'definitionIdentifier' => 'new_block',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->createBlock($blockCreateStruct, 1, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::updateBlock
     */
    public function testUpdateBlock()
    {
        $handler = $this->createLayoutHandler();

        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->name = 'My block';
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->setParameter('a_param', 'A value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->updateBlock(1, $blockUpdateStruct)
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

        self::assertInstanceOf(Layout::class, $copiedLayout);

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
                        'identifier' => 'bottom',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_left',
                        'layoutId' => 3,
                        'status' => APILayout::STATUS_DRAFT,
                    )
                ),
                new Zone(
                    array(
                        'identifier' => 'top_right',
                        'layoutId' => 3,
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

        self::assertInstanceOf(Layout::class, $copiedLayout);

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

        self::assertInstanceOf(Layout::class, $copiedLayout);

        self::assertEquals(1, $copiedLayout->id);

        $zones = $handler->loadLayoutZones($copiedLayout->id, $copiedLayout->status);

        self::assertEquals('bottom', $zones[0]->identifier);
        self::assertEquals(1, $zones[0]->layoutId);

        self::assertEquals('top_left', $zones[1]->identifier);
        self::assertEquals(1, $zones[1]->layoutId);

        self::assertEquals('top_right', $zones[2]->identifier);
        self::assertEquals(1, $zones[2]->layoutId);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::copyBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockInsertQuery
     */
    public function testCopyBlock()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->copyBlock(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::copyBlock
     */
    public function testCopyBlockToDifferentZone()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'bottom',
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->copyBlock(1, 1, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlock
     */
    public function testMoveBlock()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'bottom',
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->moveBlock(1, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::publishLayout
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::updateLayoutStatus
     */
    public function testPublishLayout()
    {
        $handler = $this->createLayoutHandler();

        $publishedLayout = $handler->publishLayout(1);

        self::assertInstanceOf(Layout::class, $publishedLayout);
        self::assertEquals(APILayout::STATUS_PUBLISHED, $publishedLayout->status);

        $archivedLayout = $handler->loadLayout(1, APILayout::STATUS_ARCHIVED);

        self::assertInstanceOf(Layout::class, $archivedLayout);

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

        // We need to delete the blocks and block items to delete the layout
        $query = $this->databaseConnection->createQueryBuilder();
        $query->delete('ngbm_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', 1);
        $query->execute();

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

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::deleteBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteBlock()
    {
        $handler = $this->createLayoutHandler();

        $handler->deleteBlock(1);
        $handler->loadBlock(1, APILayout::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::deleteLayoutBlocks
     */
    public function testDeleteLayoutBlocks()
    {
        $handler = $this->createLayoutHandler();

        $handler->deleteLayoutBlocks(1);

        self::assertEmpty($handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_PUBLISHED));
        self::assertEmpty($handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_DRAFT));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::deleteLayoutBlocks
     */
    public function testDeleteLayoutBlocksInStatus()
    {
        $handler = $this->createLayoutHandler();

        $handler->deleteLayoutBlocks(1, APILayout::STATUS_DRAFT);

        self::assertNotEmpty($handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_PUBLISHED));
        self::assertEmpty($handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_DRAFT));
    }
}
