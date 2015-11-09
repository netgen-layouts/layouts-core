<?php

namespace Netgen\BlockManager\Core\Service\Tests;

use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Exceptions\NotFoundException;

abstract class BlockServiceTest extends ServiceTest
{
    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::buildDomainBlockObject
     */
    public function testLoadBlock()
    {
        $blockService = $this->createBlockService();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'viewType' => 'default',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                )
            ),
            $blockService->loadBlock(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testLoadBlockThrowsInvalidArgumentExceptionOnInvalidId()
    {
        $blockService = $this->createBlockService();
        $blockService->loadBlock(42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testLoadBlockThrowsInvalidArgumentExceptionOnEmptyId()
    {
        $blockService = $this->createBlockService();
        $blockService->loadBlock('');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exceptions\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $blockService = $this->createBlockService();
        $blockService->loadBlock(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadZoneBlocks
     */
    public function testLoadZoneBlocks()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        self::assertEquals(
            array(
                new Block(
                    array(
                        'id' => 1,
                        'zoneId' => 2,
                        'definitionIdentifier' => 'paragraph',
                        'parameters' => array(
                            'some_param' => 'some_value',
                        ),
                        'viewType' => 'default',
                    )
                ),
                new Block(
                    array(
                        'id' => 2,
                        'zoneId' => 2,
                        'definitionIdentifier' => 'title',
                        'parameters' => array(
                            'other_param' => 'other_value',
                        ),
                        'viewType' => 'small',
                    )
                ),
            ),
            $blockService->loadZoneBlocks($layoutService->loadZone(2))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadLayoutBlocks
     */
    public function testLoadLayoutBlocks()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        self::assertEquals(
            array(
                'top_left' => array(),
                'top_right' => array(
                    new Block(
                        array(
                            'id' => 1,
                            'zoneId' => 2,
                            'definitionIdentifier' => 'paragraph',
                            'parameters' => array(
                                'some_param' => 'some_value',
                            ),
                            'viewType' => 'default',
                        )
                    ),
                    new Block(
                        array(
                            'id' => 2,
                            'zoneId' => 2,
                            'definitionIdentifier' => 'title',
                            'parameters' => array(
                                'other_param' => 'other_value',
                            ),
                            'viewType' => 'small',
                        )
                    ),
                ),
                'bottom' => array(),
            ),
            $blockService->loadLayoutBlocks($layoutService->loadLayout(1))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateBlock()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        $blockCreateStruct = $blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'zoneId' => 1,
                    'definitionIdentifier' => 'new_block',
                    'parameters' => array(
                        'some_param' => 'some_value',
                        'some_other_param' => 'some_other_value',
                    ),
                    'viewType' => 'default',
                )
            ),
            $blockService->createBlock(
                $blockCreateStruct,
                $layoutService->loadZone(1)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionOnInvalidIdentifier()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        $blockCreateStruct = $blockService->newBlockCreateStruct(42, 'default');

        $blockService->createBlock($blockCreateStruct, $layoutService->loadZone(1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionOnEmptyIdentifier()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        $blockCreateStruct = $blockService->newBlockCreateStruct('', 'default');

        $blockService->createBlock($blockCreateStruct, $layoutService->loadZone(1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionOnInvalidViewType()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        $blockCreateStruct = $blockService->newBlockCreateStruct('new_block', 42);

        $blockService->createBlock($blockCreateStruct, $layoutService->loadZone(1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionOnEmptyViewType()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        $blockCreateStruct = $blockService->newBlockCreateStruct('new_block', '');

        $blockService->createBlock($blockCreateStruct, $layoutService->loadZone(1));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlock()
    {
        $blockService = $this->createBlockService();

        $blockUpdateStruct = $blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'test_param' => 'test_value',
                        'some_other_test_param' => 'some_other_test_value',
                    ),
                    'viewType' => 'small',
                )
            ),
            $blockService->updateBlock(
                $blockService->loadBlock(1),
                $blockUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testUpdateBlockThrowsInvalidArgumentExceptionOnInvalidViewType()
    {
        $blockService = $this->createBlockService();

        $blockUpdateStruct = $blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 42;

        $blockService->updateBlock($blockService->loadBlock(1), $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testUpdateBlockThrowsInvalidArgumentExceptionOnEmptyViewType()
    {
        $blockService = $this->createBlockService();

        $blockUpdateStruct = $blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = '';

        $blockService->updateBlock($blockService->loadBlock(1), $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlock()
    {
        $blockService = $this->createBlockService();

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                )
            ),
            $blockService->copyBlock(
                $blockService->loadBlock(1)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockToDifferentZone()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        self::assertEquals(
            new Block(
                array(
                    'id' => 5,
                    'zoneId' => 3,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                )
            ),
            $blockService->copyBlock(
                $blockService->loadBlock(1),
                $layoutService->loadZone(3)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testCopyBlockThrowsInvalidArgumentExceptionOnDifferentLayout()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        $blockService->copyBlock(
            $blockService->loadBlock(1),
            $layoutService->loadZone(4)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlock()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 3,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'viewType' => 'default',
                )
            ),
            $blockService->moveBlock(
                $blockService->loadBlock(1),
                $layoutService->loadZone(3)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionOnDifferentLayout()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        $blockService->moveBlock(
            $blockService->loadBlock(1),
            $layoutService->loadZone(4)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionOnSameZone()
    {
        $blockService = $this->createBlockService();
        $layoutService = $this->createLayoutService();

        $blockService->moveBlock(
            $blockService->loadBlock(1),
            $layoutService->loadZone(2)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     */
    public function testDeleteBlock()
    {
        $blockService = $this->createBlockService();

        $block = $blockService->loadBlock(1);
        $blockService->deleteBlock($block);

        try {
            $blockService->loadBlock($block->getId());
            $this->fail('Failed to delete block with ID ' . $block->getId());
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct()
    {
        $blockService = $this->createBlockService();

        self::assertEquals(
            new BlockCreateStruct(
                array(
                    'definitionIdentifier' => 'new_block',
                    'viewType' => 'small',
                )
            ),
            $blockService->newBlockCreateStruct('new_block', 'small')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStruct()
    {
        $blockService = $this->createBlockService();

        self::assertEquals(
            new BlockUpdateStruct(),
            $blockService->newBlockUpdateStruct()
        );
    }
}
