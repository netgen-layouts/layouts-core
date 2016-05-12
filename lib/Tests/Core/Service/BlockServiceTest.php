<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;

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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionValidatorMock;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

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

        $this->collectionValidatorMock = $this->getMockBuilder(CollectionValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockService = $this->createBlockService($this->blockValidatorMock);
        $this->layoutService = $this->createLayoutService($this->layoutValidatorMock);
        $this->collectionService = $this->createCollectionService($this->collectionValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlock()
    {
        $this->blockValidatorMock
            ->expects($this->at(0))
            ->method('validateId')
            ->with($this->equalTo(1), $this->equalTo('blockId'));

        $block = $this->blockService->loadBlock(1);

        self::assertInstanceOf(APIBlock::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\API\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $this->blockService->loadBlock(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockCollections
     */
    public function testLoadBlockCollections()
    {
        $collections = $this->blockService->loadBlockCollections(
            $this->blockService->loadBlock(1)
        );

        self::assertNotEmpty($collections);

        foreach ($collections as $collection) {
            self::assertInstanceOf(CollectionReference::class, $collection);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $this->blockValidatorMock
            ->expects($this->at(0))
            ->method('validateIdentifier')
            ->with($this->equalTo('top_right'), $this->equalTo('zoneIdentifier'));

        $this->blockValidatorMock
            ->expects($this->at(1))
            ->method('validatePosition')
            ->with($this->equalTo(1), $this->equalTo('position'));

        $this->blockValidatorMock
            ->expects($this->at(2))
            ->method('validateBlockCreateStruct')
            ->with($this->equalTo($blockCreateStruct));

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayout(1, Layout::STATUS_DRAFT),
            'top_right',
            1
        );

        self::assertInstanceOf(APIBlock::class, $block);

        $secondBlock = $this->blockService->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(2, $secondBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockWithNoPosition()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->name = 'My block';
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $this->blockValidatorMock
            ->expects($this->at(0))
            ->method('validateIdentifier')
            ->with($this->equalTo('top_right'), $this->equalTo('zoneIdentifier'));

        $this->blockValidatorMock
            ->expects($this->at(1))
            ->method('validatePosition')
            ->with($this->equalTo(null), $this->equalTo('position'));

        $this->blockValidatorMock
            ->expects($this->at(2))
            ->method('validateBlockCreateStruct')
            ->with($this->equalTo($blockCreateStruct));

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayout(1, Layout::STATUS_DRAFT),
            'top_right'
        );

        self::assertInstanceOf(APIBlock::class, $block);
        self::assertEquals(3, $block->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     */
    public function testCreateBlockWithBlankName()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $this->blockValidatorMock
            ->expects($this->at(0))
            ->method('validateIdentifier')
            ->with($this->equalTo('top_right'), $this->equalTo('zoneIdentifier'));

        $this->blockValidatorMock
            ->expects($this->at(1))
            ->method('validatePosition')
            ->with($this->equalTo(2), $this->equalTo('position'));

        $this->blockValidatorMock
            ->expects($this->at(2))
            ->method('validateBlockCreateStruct')
            ->with($this->equalTo($blockCreateStruct));

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayout(1, Layout::STATUS_DRAFT),
            'top_right',
            2
        );

        self::assertInstanceOf(APIBlock::class, $block);
        self::assertEquals('', $block->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayout(1, Layout::STATUS_DRAFT),
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
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('new_block', 'default');
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayout(1, Layout::STATUS_DRAFT),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlock()
    {
        $block = $this->blockService->loadBlock(1, Layout::STATUS_DRAFT);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $this->blockValidatorMock
            ->expects($this->at(0))
            ->method('validateBlockUpdateStruct')
            ->with($this->equalTo($block), $this->equalTo($blockUpdateStruct));

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertInstanceOf(APIBlock::class, $block);
        self::assertEquals('small', $block->getViewType());
        self::assertEquals('Super cool block', $block->getName());
        self::assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'some_param' => 'some_value',
            ),
            $block->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlockWithBlankName()
    {
        $block = $this->blockService->loadBlock(1, Layout::STATUS_DRAFT);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertInstanceOf(APIBlock::class, $block);
        self::assertEquals('small', $block->getViewType());
        self::assertEquals('My block', $block->getName());
        self::assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'some_param' => 'some_value',
            ),
            $block->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlockWithBlankViewType()
    {
        $block = $this->blockService->loadBlock(1, Layout::STATUS_DRAFT);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct();
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $this->blockValidatorMock
            ->expects($this->at(0))
            ->method('validateBlockUpdateStruct')
            ->with($this->equalTo($block), $this->equalTo($blockUpdateStruct));

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertInstanceOf(APIBlock::class, $block);
        self::assertEquals('default', $block->getViewType());
        self::assertEquals('Super cool block', $block->getName());
        self::assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'some_param' => 'some_value',
            ),
            $block->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlock()
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT)
        );

        self::assertInstanceOf(APIBlock::class, $copiedBlock);
        self::assertEquals(6, $copiedBlock->getId());

        $copiedCollection = $this->collectionService->loadCollection(4, Collection::STATUS_DRAFT);
        self::assertInstanceOf(Collection::class, $copiedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     */
    public function testCopyBlockToDifferentZone()
    {
        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validateIdentifier')
            ->with($this->equalTo('bottom'), $this->equalTo('zoneIdentifier'));

        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT),
            'bottom'
        );

        self::assertInstanceOf(APIBlock::class, $copiedBlock);
        self::assertEquals(6, $copiedBlock->getId());
        self::assertEquals('bottom', $copiedBlock->getZoneIdentifier());

        $copiedCollection = $this->collectionService->loadCollection(4, Collection::STATUS_DRAFT);
        self::assertInstanceOf(Collection::class, $copiedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testCopyBlockWithNonExistingZoneThrowsBadStateException()
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlock()
    {
        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validatePosition')
            ->with($this->equalTo(1), $this->equalTo('position'));

        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT),
            1
        );

        self::assertInstanceOf(APIBlock::class, $movedBlock);
        self::assertEquals(1, $movedBlock->getId());
        self::assertEquals(1, $movedBlock->getPosition());

        $secondBlock = $this->blockService->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(0, $secondBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     */
    public function testMoveBlockToDifferentZone()
    {
        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validatePosition')
            ->with($this->equalTo(0), $this->equalTo('position'));

        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validateIdentifier')
            ->with($this->equalTo('bottom'), $this->equalTo('zoneIdentifier'));

        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT),
            0,
            'bottom'
        );

        self::assertInstanceOf(APIBlock::class, $movedBlock);
        self::assertEquals(1, $movedBlock->getId());
        self::assertEquals('bottom', $movedBlock->getZoneIdentifier());
        self::assertEquals(0, $movedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT),
            9999,
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\API\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionWhenZoneDoesNotExist()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT),
            0,
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     */
    public function testDeleteBlock()
    {
        $block = $this->blockService->loadBlock(1, Layout::STATUS_DRAFT);
        $this->blockService->deleteBlock($block);

        try {
            $this->blockService->loadBlock($block->getId(), Layout::STATUS_DRAFT);
            self::fail('Block still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondBlock = $this->blockService->loadBlock(2, Layout::STATUS_DRAFT);
        self::assertEquals(0, $secondBlock->getPosition());

        try {
            $this->collectionService->loadCollection(1, Layout::STATUS_DRAFT);
            self::fail('Collection still exists after deleting a block.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        // Verify that named collection still exists
        $this->collectionService->loadCollection(3, Collection::STATUS_DRAFT);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct()
    {
        self::assertEquals(
            new BlockCreateStruct(
                array(
                    'definitionIdentifier' => 'new_block',
                    'viewType' => 'small',
                )
            ),
            $this->blockService->newBlockCreateStruct('new_block', 'small')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStruct()
    {
        self::assertEquals(
            new BlockUpdateStruct(),
            $this->blockService->newBlockUpdateStruct()
        );
    }
}
