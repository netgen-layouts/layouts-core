<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;

abstract class BlockServiceTest extends ServiceTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockValidatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutValidatorMock;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->blockValidatorMock = $this->getMockBuilder('Netgen\BlockManager\Core\Service\Validator\BlockValidator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutValidatorMock = $this->getMockBuilder('Netgen\BlockManager\Core\Service\Validator\LayoutValidator')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::buildDomainBlockObject
     */
    public function testLoadBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'viewType' => 'default',
                    'name' => 'My block',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                    'status' => Layout::STATUS_PUBLISHED,
                )
            ),
            $blockService->loadBlock(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadBlockThrowsInvalidArgumentExceptionOnInvalidId()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $blockService->loadBlock(42.24);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testLoadBlockThrowsInvalidArgumentExceptionOnEmptyId()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $blockService->loadBlock('');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $blockService->loadBlock(PHP_INT_MAX);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadZoneBlocks
     */
    public function testLoadZoneBlocks()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

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
                        'name' => 'My block',
                        'status' => Layout::STATUS_PUBLISHED,
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
                        'name' => 'My other block',
                        'status' => Layout::STATUS_PUBLISHED,
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
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

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
                            'name' => 'My block',
                            'status' => Layout::STATUS_PUBLISHED,
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
                            'name' => 'My other block',
                            'status' => Layout::STATUS_PUBLISHED,
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
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockCreateStruct = $blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validateBlockCreateStruct')
            ->with($this->equalTo($blockCreateStruct));

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
                    'name' => 'My block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $blockService->createBlock(
                $blockCreateStruct,
                $layoutService->loadZone(1, Layout::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockWithBlankName()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockCreateStruct = $blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validateBlockCreateStruct')
            ->with($this->equalTo($blockCreateStruct));

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
                    'name' => '',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $blockService->createBlock(
                $blockCreateStruct,
                $layoutService->loadZone(1, Layout::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockInNonDraftZoneThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockCreateStruct = $blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $blockService->createBlock(
            $blockCreateStruct,
            $layoutService->loadZone(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $block = $blockService->loadBlock(1, Layout::STATUS_DRAFT);

        $blockUpdateStruct = $blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validateBlockUpdateStruct')
            ->with($this->equalTo($block), $this->equalTo($blockUpdateStruct));

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                        'test_param' => 'test_value',
                        'some_other_test_param' => 'some_other_test_value',
                    ),
                    'viewType' => 'small',
                    'name' => 'Super cool block',
                    'status' => Layout::STATUS_DRAFT,
                )
            ),
            $blockService->updateBlock(
                $block,
                $blockUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testUpdateBlockInNonDraftStatusThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $block = $blockService->loadBlock(1);

        $blockUpdateStruct = $blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';

        $blockService->updateBlock(
            $block,
            $blockUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlockWithBlankName()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $block = $blockService->loadBlock(1, Layout::STATUS_DRAFT);

        $blockUpdateStruct = $blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validateBlockUpdateStruct')
            ->with($this->equalTo($block), $this->equalTo($blockUpdateStruct));

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                        'test_param' => 'test_value',
                        'some_other_test_param' => 'some_other_test_value',
                    ),
                    'viewType' => 'small',
                    'name' => 'My block',
                )
            ),
            $blockService->updateBlock(
                $block,
                $blockUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlockWithBlankViewType()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $block = $blockService->loadBlock(1, Layout::STATUS_DRAFT);

        $blockUpdateStruct = $blockService->newBlockUpdateStruct();
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validateBlockUpdateStruct')
            ->with($this->equalTo($block), $this->equalTo($blockUpdateStruct));

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'parameters' => array(
                        'some_param' => 'some_value',
                        'test_param' => 'test_value',
                        'some_other_test_param' => 'some_other_test_value',
                    ),
                    'viewType' => 'default',
                    'name' => 'Super cool block',
                )
            ),
            $blockService->updateBlock(
                $block,
                $blockUpdateStruct
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

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
                    'name' => 'My block',
                )
            ),
            $blockService->copyBlock(
                $blockService->loadBlock(1, Layout::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockToDifferentZone()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

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
                    'name' => 'My block',
                )
            ),
            $blockService->copyBlock(
                $blockService->loadBlock(1, Layout::STATUS_DRAFT),
                $layoutService->loadZone(3, Layout::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCopyBlockInNonDraftStatusThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->copyBlock(
            $blockService->loadBlock(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCopyBlockToZoneInNonDraftStatusThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockService->copyBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            $layoutService->loadZone(3)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCopyBlockThrowsInvalidArgumentExceptionOnDifferentLayout()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockService->copyBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            $layoutService->loadZone(4, Layout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

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
                    'name' => 'My block',
                )
            ),
            $blockService->moveBlock(
                $blockService->loadBlock(1, Layout::STATUS_DRAFT),
                $layoutService->loadZone(3, Layout::STATUS_DRAFT)
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockInNonDraftStatusThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1),
            $layoutService->loadZone(3, Layout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockToZoneInNonDraftStatusThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            $layoutService->loadZone(3)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionOnDifferentLayout()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            $layoutService->loadZone(4, Layout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionOnSameZone()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock, $this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            $layoutService->loadZone(2, Layout::STATUS_DRAFT)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $block = $blockService->loadBlock(1);
        $blockService->deleteBlock($block);

        $blockService->loadBlock($block->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

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
        $blockService = $this->createBlockService($this->blockValidatorMock);

        self::assertEquals(
            new BlockUpdateStruct(),
            $blockService->newBlockUpdateStruct()
        );
    }
}
