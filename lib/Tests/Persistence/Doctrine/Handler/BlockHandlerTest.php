<?php

namespace Netgen\BlockManager\Tests\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\Persistence\Values\Page\Block;

class BlockHandlerTest extends \PHPUnit_Framework_TestCase
{
    use TestCase;

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
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getBlockSelectQuery
     */
    public function testLoadBlock()
    {
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
            $this->blockHandler->loadBlock(1, APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $this->blockHandler->loadBlock(999999, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadZoneBlocks
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
            $this->blockHandler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_PUBLISHED)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlockCollections
     */
    public function testLoadBlockCollections()
    {
        self::assertEquals(
            array(
                new CollectionReference(
                    array(
                        'blockId' => 1,
                        'status' => APILayout::STATUS_DRAFT,
                        'collectionId' => 1,
                        'identifier' => 'default',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
                new CollectionReference(
                    array(
                        'blockId' => 1,
                        'status' => APILayout::STATUS_DRAFT,
                        'collectionId' => 3,
                        'identifier' => 'featured',
                        'offset' => 0,
                        'limit' => null,
                    )
                ),
            ),
            $this->blockHandler->loadBlockCollections(1, APILayout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadBlockCollections
     */
    public function testLoadBlockCollectionsForNonExistingBlock()
    {
        self::assertEquals(
            array(),
            $this->blockHandler->loadBlockCollections(9999, APILayout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::loadZoneBlocks
     */
    public function testLoadZoneBlocksForNonExistingZone()
    {
        self::assertEquals(array(), $this->blockHandler->loadZoneBlocks(1, 'non_existing', APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getBlockInsertQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCreateBlock()
    {
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
            $this->blockHandler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT, 1)
        );

        $secondBlock = $this->blockHandler->loadBlock(2, APILayout::STATUS_DRAFT);
        self::assertEquals(2, $secondBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getBlockInsertQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCreateBlockWithNoPosition()
    {
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
            $this->blockHandler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockThrowsBadStateExceptionOnNegativePosition()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        $this->blockHandler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT, -5);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockThrowsBadStateExceptionOnTooLargePosition()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        $this->blockHandler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::updateBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::__construct
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::getQuery
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper::applyStatusCondition
     */
    public function testUpdateBlock()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->name = 'My block';
        $blockUpdateStruct->viewType = 'large';
        $blockUpdateStruct->setParameter('a_param', 'A value');
        $blockUpdateStruct->setParameter('some_param', 'Some other value');

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
                        'some_param' => 'Some other value',
                    ),
                    'viewType' => 'large',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->updateBlock(1, APILayout::STATUS_DRAFT, $blockUpdateStruct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCopyBlock()
    {
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
            $this->blockHandler->copyBlock(1, APILayout::STATUS_DRAFT, 'top_right')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::copyBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testCopyBlockToDifferentZone()
    {
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
            $this->blockHandler->copyBlock(1, APILayout::STATUS_DRAFT, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
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
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->moveBlock(1, APILayout::STATUS_DRAFT, 1)
        );

        $firstBlock = $this->blockHandler->loadBlock(2, APILayout::STATUS_DRAFT);
        self::assertEquals(0, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
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
                        'other_param' => 'other_value',
                    ),
                    'viewType' => 'small',
                    'name' => 'My other block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->moveBlock(2, APILayout::STATUS_DRAFT, 0)
        );

        $firstBlock = $this->blockHandler->loadBlock(1, APILayout::STATUS_DRAFT);
        self::assertEquals(1, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionOnNegativePosition()
    {
        $this->blockHandler->moveBlock(1, APILayout::STATUS_DRAFT, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->blockHandler->moveBlock(1, APILayout::STATUS_DRAFT, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToZone
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
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'My block',
                    'status' => APILayout::STATUS_DRAFT,
                )
            ),
            $this->blockHandler->moveBlockToZone(1, APILayout::STATUS_DRAFT, 'bottom', 0)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToZone
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionOnNegativePosition()
    {
        $this->blockHandler->moveBlockToZone(1, APILayout::STATUS_DRAFT, 'bottom', -1);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::moveBlockToZone
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionOnTooLargePosition()
    {
        $this->blockHandler->moveBlockToZone(1, APILayout::STATUS_DRAFT, 'bottom', 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::deleteBlock
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::getPositionHelperConditions
     */
    public function testDeleteBlock()
    {
        $this->blockHandler->deleteBlock(1, APILayout::STATUS_DRAFT);

        $secondBlock = $this->blockHandler->loadBlock(2, APILayout::STATUS_DRAFT);
        self::assertEquals(0, $secondBlock->position);

        try {
            $this->blockHandler->loadBlock(1, APILayout::STATUS_DRAFT);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::collectionExists
     */
    public function testCollectionExists()
    {
        self::assertTrue($this->blockHandler->collectionExists(1, APILayout::STATUS_DRAFT, 1));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::collectionExists
     */
    public function testCollectionNotExists()
    {
        self::assertFalse($this->blockHandler->collectionExists(1, APILayout::STATUS_DRAFT, 5));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::collectionIdentifierExists
     */
    public function testCollectionIdentifierExists()
    {
        self::assertTrue($this->blockHandler->collectionIdentifierExists(1, APILayout::STATUS_DRAFT, 'default'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::collectionIdentifierExists
     */
    public function testCollectionIdentifierNotExists()
    {
        self::assertFalse($this->blockHandler->collectionIdentifierExists(1, APILayout::STATUS_DRAFT, 'something_else'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::addCollectionToBlock
     */
    public function testAddCollectionToBlock()
    {
        $this->blockHandler->addCollectionToBlock(1, APILayout::STATUS_DRAFT, 2, 'new');

        self::assertTrue($this->blockHandler->collectionExists(1, APILayout::STATUS_DRAFT, 2));
        self::assertTrue($this->blockHandler->collectionIdentifierExists(1, APILayout::STATUS_DRAFT, 'new'));
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Doctrine\Handler\BlockHandler::removeCollectionFromBlock
     */
    public function testRemoveCollectionFromBlock()
    {
        $this->blockHandler->removeCollectionFromBlock(1, APILayout::STATUS_DRAFT, 1);

        self::assertFalse($this->blockHandler->collectionExists(1, APILayout::STATUS_DRAFT, 1));
        self::assertFalse($this->blockHandler->collectionIdentifierExists(1, APILayout::STATUS_DRAFT, 'default'));
    }
}
