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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::applyStatusCondition
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutSelectQuery
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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayout
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadLayoutThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadLayout(999999, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createZoneSelectQuery
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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingLayout()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadZone(999999, 'bottom', APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZone
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadZoneThrowsNotFoundExceptionOnNonExistingZone()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadZone(1, 'non_existing', APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::layoutExists
     */
    public function testLayoutExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(true, $handler->layoutExists(1, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::layoutExists
     */
    public function testLayoutNotExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->layoutExists(999999, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::layoutExists
     */
    public function testLayoutNotExistsInStatus()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->layoutExists(1, APILayout::STATUS_ARCHIVED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::zoneExists
     */
    public function testZoneExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(true, $handler->zoneExists(1, 'top_left', APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::zoneExists
     */
    public function testZoneNotExists()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->zoneExists(1, 'non_existing', APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::zoneExists
     */
    public function testZoneNotExistsInStatus()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(false, $handler->zoneExists(1, 'top_left', APILayout::STATUS_ARCHIVED));
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
            $handler->loadLayoutZones(1, APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadLayoutZones
     */
    public function testLoadLayoutZonesForNonExistingLayout()
    {
        $handler = $this->createLayoutHandler();
        self::assertEquals(array(), $handler->loadLayoutZones(999999, APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createBlockSelectQuery
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
            $handler->loadBlock(1, APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $handler = $this->createLayoutHandler();
        $handler->loadBlock(999999, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZoneBlocks
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
                        'status' => APILayout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::loadZoneBlocks
     */
    public function testLoadZoneBlocksForNonExistingZone()
    {
        $handler = $this->createLayoutHandler();
        self::assertEquals(array(), $handler->loadZoneBlocks(1, 'non_existing', APILayout::STATUS_PUBLISHED));
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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createBlockInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::incrementBlockPositions
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::getNextBlockPosition
     */
    public function testCreateBlock()
    {
        $handler = $this->createLayoutHandler();

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->position = 1;
        $blockCreateStruct->setParameter('a_param', 'A value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 1,
                    'definitionIdentifier' => 'new_block',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT)
        );

        $secondBlock = $handler->loadBlock(2, APILayout::STATUS_DRAFT);
        self::assertEquals(2, $secondBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createBlockInsertQuery
     */
    public function testCreateBlockWithBlankPosition()
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
                    'zoneIdentifier' => 'top_right',
                    'position' => 2,
                    'definitionIdentifier' => 'new_block',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createBlockInsertQuery
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockInNonExistingZoneThrowsBadStateException()
    {
        $handler = $this->createLayoutHandler();

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        $handler->createBlock($blockCreateStruct, 1, 'non_existing', APILayout::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::updateBlock
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
                    'position' => 0,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->updateBlock(1, APILayout::STATUS_DRAFT, $blockUpdateStruct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::copyLayout
     */
    public function testCopyLayout()
    {
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutStatus
     */
    public function testCreateLayoutStatus()
    {
        $handler = $this->createLayoutHandler();
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
            $handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_ARCHIVED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::createLayoutStatus
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateLayoutStatusThrowsBadStateException()
    {
        $handler = $this->createLayoutHandler();
        $handler->createLayoutStatus(1, APILayout::STATUS_PUBLISHED, APILayout::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::updateLayoutStatus
     */
    public function testUpdateLayoutStatus()
    {
        $handler = $this->createLayoutHandler();
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
            $handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_ARCHIVED)
        );

        try {
            $handler->loadLayout(1, APILayout::STATUS_DRAFT);
            self::fail('Layout in old status still exists after updating the status');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::updateLayoutStatus
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testUpdateLayoutStatusThrowsBadStateException()
    {
        $handler = $this->createLayoutHandler();
        $handler->updateLayoutStatus(1, APILayout::STATUS_DRAFT, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::copyBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::getNextBlockPosition
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
                    'position' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->copyBlock(1, APILayout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::copyBlock
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
                    'position' => 0,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->copyBlock(1, APILayout::STATUS_DRAFT, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::copyBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCopyBlockInNonExistingZoneThrowsBadStateException()
    {
        $handler = $this->createLayoutHandler();

        $handler->copyBlock(1, APILayout::STATUS_DRAFT, 'non_existing');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::moveBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::incrementBlockPositions
     */
    public function testMoveBlock()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 1,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->moveBlock(1, APILayout::STATUS_DRAFT, 1)
        );

        $secondBlock = $handler->loadBlock(2, APILayout::STATUS_DRAFT);
        self::assertEquals(2, $secondBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::moveBlock
     */
    public function testMoveBlockToDifferentZone()
    {
        $handler = $this->createLayoutHandler();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'bottom',
                    'position' => 0,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $handler->moveBlock(1, APILayout::STATUS_DRAFT, 0, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockInNonExistingZoneThrowsBadStateException()
    {
        $handler = $this->createLayoutHandler();

        $handler->moveBlock(1, APILayout::STATUS_DRAFT, 0, 'non_existing');
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

        $handler->loadLayout(1, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::deleteLayout
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

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Handler::deleteBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteBlock()
    {
        $handler = $this->createLayoutHandler();

        $handler->deleteBlock(1, APILayout::STATUS_DRAFT);
        $handler->loadBlock(1, APILayout::STATUS_DRAFT);
    }
}
