<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
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
     * Tears down the tests.
     */
    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockSelectQuery
     */
    public function testLoadBlock()
    {
        $handler = $this->createBlockHandler();

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $handler = $this->createBlockHandler();
        $handler->loadBlock(999999, APILayout::STATUS_PUBLISHED);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadZoneBlocks
     */
    public function testLoadZoneBlocks()
    {
        $handler = $this->createBlockHandler();

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadZoneBlocks
     */
    public function testLoadZoneBlocksForNonExistingZone()
    {
        $handler = $this->createBlockHandler();
        self::assertEquals(array(), $handler->loadZoneBlocks(1, 'non_existing', APILayout::STATUS_PUBLISHED));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockInsertQuery
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::incrementBlockPositions
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::getNextBlockPosition
     */
    public function testCreateBlock()
    {
        $handler = $this->createBlockHandler();

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
            $handler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT, 1)
        );

        $secondBlock = $handler->loadBlock(2, APILayout::STATUS_DRAFT);
        self::assertEquals(2, $secondBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockInsertQuery
     */
    public function testCreateBlockWithNoPosition()
    {
        $handler = $this->createBlockHandler();

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockThrowsBadStateExceptionOnNegativePosition()
    {
        $handler = $this->createBlockHandler();

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        $handler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT, -5);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockThrowsBadStateExceptionOnTooLargePosition()
    {
        $handler = $this->createBlockHandler();

        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->definitionIdentifier = 'new_block';
        $blockCreateStruct->viewType = 'large';
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('a_param', 'A value');

        $handler->createBlock($blockCreateStruct, 1, 'top_right', APILayout::STATUS_DRAFT, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::updateBlock
     */
    public function testUpdateBlock()
    {
        $handler = $this->createBlockHandler();

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::copyBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::getNextBlockPosition
     */
    public function testCopyBlock()
    {
        $handler = $this->createBlockHandler();

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
            $handler->copyBlock(1, APILayout::STATUS_DRAFT, 'top_right')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::copyBlock
     */
    public function testCopyBlockToDifferentZone()
    {
        $handler = $this->createBlockHandler();

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::incrementBlockPositions
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::decrementBlockPositions
     */
    public function testMoveBlock()
    {
        $handler = $this->createBlockHandler();

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

        $firstBlock = $handler->loadBlock(2, APILayout::STATUS_DRAFT);
        self::assertEquals(0, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::incrementBlockPositions
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::decrementBlockPositions
     */
    public function testMoveBlockToLowerPosition()
    {
        $handler = $this->createBlockHandler();

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
            $handler->moveBlock(2, APILayout::STATUS_DRAFT, 0)
        );

        $firstBlock = $handler->loadBlock(1, APILayout::STATUS_DRAFT);
        self::assertEquals(1, $firstBlock->position);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionOnNegativePosition()
    {
        $handler = $this->createBlockHandler();

        $handler->moveBlock(1, APILayout::STATUS_DRAFT, -1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionOnTooLargePosition()
    {
        $handler = $this->createBlockHandler();

        $handler->moveBlock(1, APILayout::STATUS_DRAFT, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlockToZone
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::incrementBlockPositions
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::decrementBlockPositions
     */
    public function testMoveBlockToZone()
    {
        $handler = $this->createBlockHandler();

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
            $handler->moveBlockToZone(1, APILayout::STATUS_DRAFT, 'bottom', 0)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlockToZone
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionOnNegativePosition()
    {
        $handler = $this->createBlockHandler();

        $handler->moveBlockToZone(1, APILayout::STATUS_DRAFT, 'bottom', -1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::moveBlockToZone
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionOnTooLargePosition()
    {
        $handler = $this->createBlockHandler();

        $handler->moveBlockToZone(1, APILayout::STATUS_DRAFT, 'bottom', 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::deleteBlock
     */
    public function testDeleteBlock()
    {
        $handler = $this->createBlockHandler();

        $handler->deleteBlock(1, APILayout::STATUS_DRAFT);

        try {
            $handler->loadBlock(1, APILayout::STATUS_DRAFT);
            self::fail('Block still exists after deleting');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondBlock = $handler->loadBlock(2, APILayout::STATUS_DRAFT);
        self::assertEquals(0, $secondBlock->position);
    }
}
