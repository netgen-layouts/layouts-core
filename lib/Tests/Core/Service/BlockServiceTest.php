<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Exception\NotFoundException;
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
                    'position' => 0,
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
                    'zoneIdentifier' => 'top_right',
                    'position' => 1,
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
                'top_right',
                1
            )
        );

        $secondBlock = $blockService->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(2, $secondBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockWithNoPosition()
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
                    'zoneIdentifier' => 'top_right',
                    'position' => 2,
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
                'top_right'
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
                    'zoneIdentifier' => 'top_right',
                    'position' => 2,
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
                'top_right',
                2
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionWithInvalidZoneIdentifier()
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
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionWithInvalidPosition()
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
            'top_left',
            '0'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionWithEmptyZoneIdentifier()
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
            ''
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionWhenPositionIsNegative()
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
            'top_right',
            -5
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
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
            'top_right',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockWithNonExistingZoneThrowsBadStateException()
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
                    'position' => 0,
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
                    'position' => 0,
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
                    'position' => 0,
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
                    'position' => 2,
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
                    'position' => 0,
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
    public function testCopyBlockThrowsInvalidArgumentExceptionOnInvalidZoneIdentifier()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->copyBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testCopyBlockThrowsInvalidArgumentExceptionOnEmptyZoneIdentifier()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->copyBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            ''
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCopyBlockWithNonExistingZoneThrowsBadStateException()
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
                    'zoneIdentifier' => 'top_right',
                    'position' => 1,
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
                1,
                'top_right'
            )
        );

        $secondBlock = $blockService->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(0, $secondBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockToDifferentZone()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

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
                )
            ),
            $blockService->moveBlock(
                $blockService->loadBlock(1, Layout::STATUS_DRAFT),
                0,
                'bottom'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionWhenPositionIsNotInteger()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            '0',
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionWhenPositionIsNegative()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            -5,
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            9999,
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionOnInvalidZone()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            0,
            42
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionOnEmptyZone()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            0,
            ''
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionWhenZoneDoesNotExist()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $blockService->moveBlock(
            $blockService->loadBlock(1, Layout::STATUS_DRAFT),
            0,
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
            0,
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     */
    public function testDeleteBlock()
    {
        $blockService = $this->createBlockService($this->blockValidatorMock);

        $block = $blockService->loadBlock(1, Layout::STATUS_DRAFT);
        $blockService->deleteBlock($block);

        try {
            $blockService->loadBlock($block->getId(), Layout::STATUS_DRAFT);
            self::fail('Block still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondBlock = $blockService->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(0, $secondBlock->getPosition());
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
