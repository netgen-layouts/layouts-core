<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone as LayoutTypeZone;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
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
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

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

        $layoutType = new LayoutType(
            '3_zones_a',
            true,
            '3 zones A',
            array(
                'top_left' => new LayoutTypeZone('top_left', 'Top left', array()),
                'top_right' => new LayoutTypeZone('top_right', 'Top right', array('title')),
                'bottom' => new LayoutTypeZone('bottom', 'Bottom', array('title')),
            )
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry();
        $this->layoutTypeRegistry->addLayoutType('3_zones_a', $layoutType);

        $this->blockService = $this->createBlockService(
            $this->blockValidatorMock,
            $this->layoutTypeRegistry
        );

        $this->layoutService = $this->createLayoutService(
            $this->layoutValidatorMock,
            $this->layoutTypeRegistry
        );

        $this->collectionService = $this->createCollectionService(
            $this->collectionValidatorMock
        );
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
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $this->blockService->loadBlock(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadCollectionReferences
     */
    public function testLoadCollectionReferences()
    {
        $collections = $this->blockService->loadCollectionReferences(
            $this->blockService->loadBlock(1)
        );

        self::assertNotEmpty($collections);

        foreach ($collections as $collection) {
            self::assertInstanceOf(CollectionReference::class, $collection);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('title', 'default');
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

        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        self::assertCount(1, $collectionReferences);

        self::assertEquals('default', $collectionReferences[0]->getIdentifier());
        self::assertEquals(0, $collectionReferences[0]->getOffset());
        self::assertNull($collectionReferences[0]->getLimit());

        $collection = $this->collectionService->loadCollection(4, Layout::STATUS_DRAFT);
        self::assertEquals(Collection::TYPE_MANUAL, $collection->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockWithNoPosition()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('title', 'default');
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockWithBlankName()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('title', 'default');
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('title', 'default');
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateBlockWithNonExistingZoneThrowsBadStateException()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('title', 'default');
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateBlockWithWithDisallowedIdentifierThrowsBadStateException()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct('not_allowed', 'default');
        $blockCreateStruct->name = 'My block';

        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayout(1, Layout::STATUS_DRAFT),
            'top_right'
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCopyBlockToDifferentZone()
    {
        $this->blockValidatorMock
            ->expects($this->once())
            ->method('validateIdentifier')
            ->with($this->equalTo('top_left'), $this->equalTo('zoneIdentifier'));

        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlock(1),
            'top_left'
        );

        self::assertInstanceOf(APIBlock::class, $copiedBlock);
        self::assertEquals(6, $copiedBlock->getId());
        self::assertEquals('top_left', $copiedBlock->getZoneIdentifier());

        $copiedCollection = $this->collectionService->loadCollection(4);
        self::assertInstanceOf(Collection::class, $copiedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCopyBlockWithNonExistingZoneThrowsBadStateException()
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlock(2),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCopyBlockWithDisallowedIdentifierThrowsBadStateException()
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT),
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
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
            $this->blockService->loadBlock(2),
            0,
            'bottom'
        );

        self::assertInstanceOf(APIBlock::class, $movedBlock);
        self::assertEquals(2, $movedBlock->getId());
        self::assertEquals('bottom', $movedBlock->getZoneIdentifier());
        self::assertEquals(0, $movedBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveBlockThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlock(2),
            9999,
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionWhenZoneDoesNotExist()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlock(2),
            0,
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveBlockThrowsBadStateExceptionWithDisallowedIdentifier()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlock(1, Layout::STATUS_DRAFT),
            0,
            'bottom'
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
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct()
    {
        self::assertEquals(
            new BlockCreateStruct(
                array(
                    'definitionIdentifier' => 'title',
                    'viewType' => 'small',
                )
            ),
            $this->blockService->newBlockCreateStruct('title', 'small')
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
