<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Collection\Collection;
use Netgen\BlockManager\Persistence\Values\Collection\Item;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use PHPUnit\Framework\TestCase;

class BlockHandlerTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Handler\LayoutHandler
     */
    protected $layoutHandler;

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

        $this->blockHandler = $this->createBlockHandler();
        $this->layoutHandler = $this->createLayoutHandler();
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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockData
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::getBlockSelectQuery
     */
    public function testLoadBlock()
    {
        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 0,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'content' => 'Paragraph',
                    ),
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
            $this->blockHandler->loadBlock(1, Layout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadBlockData
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $this->blockHandler->loadBlock(999999, Layout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockExists()
    {
        self::assertTrue($this->blockHandler->blockExists(1, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockNotExists()
    {
        self::assertFalse($this->blockHandler->blockExists(999999, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::blockExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::blockExists
     */
    public function testBlockNotExistsInStatus()
    {
        self::assertFalse($this->blockHandler->blockExists(6, Layout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadZoneBlocks
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadZoneBlocksData
     */
    public function testLoadZoneBlocks()
    {
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
                            'content' => 'Paragraph',
                        ),
                        'viewType' => 'text',
                        'itemViewType' => 'standard',
                        'name' => 'My block',
                        'status' => Layout::STATUS_PUBLISHED,
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
                            'tag' => 'h1',
                            'title' => 'Title',
                        ),
                        'viewType' => 'title',
                        'itemViewType' => 'standard',
                        'name' => 'My other block',
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
                new Block(
                    array(
                        'id' => 5,
                        'layoutId' => 1,
                        'zoneIdentifier' => 'top_right',
                        'position' => 2,
                        'definitionIdentifier' => 'title',
                        'parameters' => array(
                            'tag' => 'h3',
                            'title' => 'Title',
                        ),
                        'viewType' => 'title',
                        'itemViewType' => 'standard',
                        'name' => 'My fourth block',
                        'status' => Layout::STATUS_PUBLISHED,
                    )
                ),
            ),
            $this->blockHandler->loadZoneBlocks(
                $this->layoutHandler->loadZone(
                    $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED),
                    'top_right'
                )
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadCollectionReferences
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::loadCollectionReferencesData
     */
    public function testLoadCollectionReferences()
    {
        self::assertEquals(
            array(
                new CollectionReference(
                    array(
                        'blockId' => 1,
                        'blockStatus' => Layout::STATUS_DRAFT,
                        'collectionId' => 1,
                        'collectionStatus' => Collection::STATUS_DRAFT,
                        'identifier' => 'default',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
                new CollectionReference(
                    array(
                        'blockId' => 1,
                        'blockStatus' => Layout::STATUS_DRAFT,
                        'collectionId' => 3,
                        'collectionStatus' => Collection::STATUS_PUBLISHED,
                        'identifier' => 'featured',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
            ),
            $this->blockHandler->loadCollectionReferences(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCreateBlock()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 7,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 1,
                    'definitionIdentifier' => 'new_block',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->layoutHandler->loadLayout(1, Layout::STATUS_DRAFT),
                'top_right',
                1
            )
        );

        $secondBlock = $this->blockHandler->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(2, $secondBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCreateBlockWithNoPosition()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 7,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 3,
                    'definitionIdentifier' => 'new_block',
                    'parameters' => array(
                        'a_param' => 'A value',
                    ),
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->createBlock(
                $blockCreateStruct,
                $this->layoutHandler->loadLayout(1, Layout::STATUS_DRAFT),
                'top_right'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateBlockThrowsBadStateExceptionOnNegativePosition()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED),
            'top_right',
            -5
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateBlockThrowsBadStateExceptionOnTooLargePosition()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        $this->blockHandler->createBlock(
            $blockCreateStruct,
            $this->layoutHandler->loadLayout(1, Layout::STATUS_PUBLISHED),
            'top_right',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::updateBlock
     */
    public function testUpdateBlock()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->name = 'My block';
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->itemViewType = 'new';
        $blockUpdateStruct->setParameter('content', 'new_value');
        $blockUpdateStruct->setParameter('some_param', 'Some value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 0,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'content' => 'new_value',
                        'some_param' => 'Some value',
                    ),
                    'viewType' => 'large',
                    'itemViewType' => 'new',
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->updateBlock(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                $blockUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCopyBlock()
    {
        self::assertEquals(
            new Block(
                array(
                    'id' => 7,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 3,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'content' => 'Paragraph',
                    ),
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                'top_right'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCopyBlockToDifferentZone()
    {
        self::assertEquals(
            new Block(
                array(
                    'id' => 7,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'bottom',
                    'position' => 0,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'content' => 'Paragraph',
                    ),
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->copyBlock(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                'bottom'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testMoveBlock()
    {
        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 1,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'content' => 'Paragraph',
                    ),
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->moveBlock(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                1
            )
        );

        $firstBlock = $this->blockHandler->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(0, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testMoveBlockToLowerPosition()
    {
        self::assertEquals(
            new Block(
                array(
                    'id' => 2,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 0,
                    'definitionIdentifier' => 'title',
                    'parameters' => array(
                        'tag' => 'h1',
                        'title' => 'Title',
                    ),
                    'viewType' => 'title',
                    'itemViewType' => 'standard',
                    'name' => 'My other block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->moveBlock(
                $this->blockHandler->loadBlock(2, Layout::STATUS_DRAFT),
                0
            )
        );

        $firstBlock = $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT);
        self::assertEquals(1, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionOnNegativePosition()
    {
        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->blockHandler->moveBlock(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testMoveBlockToZone()
    {
        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'bottom',
                    'position' => 0,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'content' => 'Paragraph',
                    ),
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->moveBlockToZone(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                'bottom',
                0
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionOnNegativePosition()
    {
        $this->blockHandler->moveBlockToZone(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
            'bottom',
            -1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToZone
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->blockHandler->moveBlockToZone(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
            'bottom',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockStatus
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockCollectionsStatus
     */
    public function testCreateBlockStatus()
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT)
        );

        $this->blockHandler->createBlockStatus(
            $this->blockHandler->loadBlock(1, Layout::STATUS_PUBLISHED),
            Layout::STATUS_DRAFT
        );

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
                    'position' => 0,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'content' => 'Paragraph',
                    ),
                    'viewType' => 'text',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT)
        );

        $collectionReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT)
        );

        self::assertCount(2, $collectionReferences);

        $collectionIds = array(
            $collectionReferences[0]->collectionId,
            $collectionReferences[1]->collectionId,
        );

        self::assertContains(2, $collectionIds);
        self::assertContains(3, $collectionIds);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockCollectionsStatus
     */
    public function testCreateBlockCollectionsStatus()
    {
        $this->blockHandler->createBlockCollectionsStatus(
            $this->blockHandler->loadBlock(1, Layout::STATUS_PUBLISHED),
            Layout::STATUS_DRAFT
        );

        $collectionReferences = $this->blockHandler->loadCollectionReferences(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT)
        );

        self::assertCount(3, $collectionReferences);

        $collectionIds = array(
            $collectionReferences[0]->collectionId,
            $collectionReferences[1]->collectionId,
            $collectionReferences[2]->collectionId,
        );

        self::assertContains(1, $collectionIds);
        self::assertContains(2, $collectionIds);
        self::assertContains(3, $collectionIds);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlockCollectionsStatus
     */
    public function testCreateBlockCollectionsStatusWithExistingStatus()
    {
        // First create a collection in draft status separately and delete an item from it
        $this->collectionHandler->createCollectionStatus(
            $this->collectionHandler->loadCollection(2, Collection::STATUS_PUBLISHED),
            Collection::STATUS_DRAFT
        );

        $this->collectionHandler->deleteItem(
            $this->collectionHandler->loadItem(4, Collection::STATUS_DRAFT)
        );

        // Then verify that archived status is recreated after creating a layout in archived status
        $this->blockHandler->createBlockCollectionsStatus(
            $this->blockHandler->loadBlock(1, Layout::STATUS_PUBLISHED),
            Layout::STATUS_DRAFT
        );

        $item = $this->collectionHandler->loadItem(4, Collection::STATUS_DRAFT);
        self::assertInstanceOf(Item::class, $item);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testDeleteBlock()
    {
        $this->blockHandler->deleteBlock(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT)
        );

        $secondBlock = $this->blockHandler->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(0, $secondBlock->position);

        try {
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        try {
            $this->collectionHandler->loadCollection(1, Layout::STATUS_DRAFT);
            self::fail('Collection still exists after deleting a block.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that named collection still exists
        $this->collectionHandler->loadCollection(3, Collection::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlockCollections
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteBlockCollections()
    {
        $this->blockHandler->deleteBlockCollections(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT)
        );

        // Verify that named collection still exists
        $this->collectionHandler->loadCollection(3, Collection::STATUS_PUBLISHED);

        $this->collectionHandler->loadCollection(1, Layout::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::collectionReferenceExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::collectionReferenceExists
     */
    public function testCollectionReferenceExists()
    {
        self::assertTrue(
            $this->blockHandler->collectionReferenceExists(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                'default'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::collectionReferenceExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::collectionReferenceExists
     */
    public function testCollectionReferenceNotExists()
    {
        self::assertFalse(
            $this->blockHandler->collectionReferenceExists(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                'something_else'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::collectionExists
     */
    public function testCollectionExists()
    {
        self::assertTrue(
            $this->blockHandler->collectionExists(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                $this->collectionHandler->loadCollection(1, Collection::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::collectionExists
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::collectionExists
     */
    public function testCollectionNotExists()
    {
        self::assertFalse(
            $this->blockHandler->collectionExists(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                $this->collectionHandler->loadCollection(2, Collection::STATUS_PUBLISHED)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createCollectionReference
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::createCollectionReference
     */
    public function testCreateCollectionReference()
    {
        $this->blockHandler->createCollectionReference(
            $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
            $this->collectionHandler->loadCollection(2, Collection::STATUS_PUBLISHED),
            'new'
        );

        self::assertTrue(
            $this->blockHandler->collectionReferenceExists(
                $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT),
                'new'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteCollectionReference
     * @covers \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler::deleteCollectionReference
     */
    public function testDeleteCollectionReference()
    {
        $block = $this->blockHandler->loadBlock(1, Layout::STATUS_DRAFT);

        $this->blockHandler->deleteCollectionReference(
            $this->blockHandler->loadCollectionReference(
                $block,
                'default'
            )
        );

        self::assertFalse(
            $this->blockHandler->collectionReferenceExists(
                $block,
                'default'
            )
        );
    }
}
