<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Service\Validator\BlockValidator;
use Netgen\BlockManager\API\Service\Validator\LayoutValidator;
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
        $this->blockValidatorMock = $this->getMockBuilder(BlockValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutValidatorMock = $this->getMockBuilder(LayoutValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
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
        $blockService->loadBlock(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

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
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_left',
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
                $layoutService->loadLayout(1, Layout::STATUS_DRAFT),
                'top_left'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockWithBlankName()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

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
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_left',
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
                $layoutService->loadLayout(1, Layout::STATUS_DRAFT),
                'top_left'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateBlockThrowsInvalidArgumentException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $blockCreateStruct = $blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $blockService->createBlock(
            $blockCreateStruct,
            $layoutService->loadLayout(1, Layout::STATUS_DRAFT),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockInNonDraftLayoutThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);
        $layoutService = $this->createLayoutService($this->layoutValidatorMock);

        $blockCreateStruct = $blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $blockService->createBlock(
            $blockCreateStruct,
            $layoutService->loadLayout(1),
            'top_left'
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
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
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
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
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
                    'layoutId' => 1,
                    'zoneIdentifier' => 'top_right',
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

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
                )
            ),
            $blockService->copyBlock(
                $blockService->loadBlock(1, Layout::STATUS_DRAFT),
                'bottom'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCopyBlockThrowsInvalidArgumentException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->copyBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            'non_existing'
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

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
                )
            ),
            $blockService->moveBlock(
                $blockService->loadBlock(1, Layout::STATUS_DRAFT),
                'bottom'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testMoveBlockThrowsInvalidArgumentException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockInNonDraftStatusThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1),
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testDeleteBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $block = $blockService->loadBlock(1, Layout::STATUS_DRAFT);
        $blockService->deleteBlock($block);

        $blockService->loadBlock($block->getId(), Layout::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testDeleteBlockInNonDraftStatusThrowsBadStateException()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $block = $blockService->loadBlock(1);
        $blockService->deleteBlock($block);
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
