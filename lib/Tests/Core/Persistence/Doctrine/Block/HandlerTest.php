<?php

namespace Netgen\BlockManager\Tests\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::__construct
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
            $handler->loadBlock(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $handler = $this->createBlockHandler();
        $handler->loadBlock(PHP_INT_MAX);
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
        $handler = $this->createBlockHandler();
        self::assertEquals(array(), $handler->loadZoneBlocks(1, 'non_existing'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockInsertQuery
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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::copyBlock
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::createBlockInsertQuery
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
        $handler = $this->createBlockHandler();

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
        $handler = $this->createBlockHandler();

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
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::deleteBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteBlock()
    {
        $handler = $this->createBlockHandler();

        $handler->deleteBlock(1);
        $handler->loadBlock(1, APILayout::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::deleteLayoutBlocks
     */
    public function testDeleteLayoutBlocks()
    {
        $handler = $this->createBlockHandler();

        $handler->deleteLayoutBlocks(1);

        self::assertEmpty($handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_PUBLISHED));
        self::assertEmpty($handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_DRAFT));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Handler::deleteLayoutBlocks
     */
    public function testDeleteLayoutBlocksInStatus()
    {
        $handler = $this->createBlockHandler();

        $handler->deleteLayoutBlocks(1, APILayout::STATUS_DRAFT);

        self::assertNotEmpty($handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_PUBLISHED));
        self::assertEmpty($handler->loadZoneBlocks(1, 'top_right', APILayout::STATUS_DRAFT));
    }
}
