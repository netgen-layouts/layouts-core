<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\LayoutType\Zone as LayoutTypeZone;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Values\Page\BlockDraft as APIBlockDraft;

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
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

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
        $this->blockValidatorMock = $this->createMock(BlockValidator::class);

        $this->layoutValidatorMock = $this->createMock(LayoutValidator::class);

        $this->collectionValidatorMock = $this->createMock(CollectionValidator::class);

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
        $this->layoutTypeRegistry->addLayoutType($layoutType);

        $blockDefinition1 = new BlockDefinition(
            'title',
            $this->createMock(BlockDefinitionHandlerInterface::class),
            new Configuration('title', array(), array())
        );

        $blockDefinition2 = new BlockDefinition(
            'gallery',
            $this->createMock(BlockDefinitionHandlerInterface::class),
            new Configuration('gallery', array(), array())
        );

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition1);
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition2);

        $this->blockService = $this->createBlockService(
            $this->blockValidatorMock,
            $this->layoutTypeRegistry,
            $this->blockDefinitionRegistry
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     */
    public function testLoadBlockDraft()
    {
        $block = $this->blockService->loadBlockDraft(1);

        self::assertInstanceOf(APIBlockDraft::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadBlockDraftThrowsNotFoundException()
    {
        $this->blockService->loadBlockDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isPublished
     */
    public function testIsPublished()
    {
        $block = $this->blockService->loadBlock(1);

        self::assertTrue($this->blockService->isPublished($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isPublished
     */
    public function testIsPublishedReturnsFalse()
    {
        $block = $this->blockService->loadBlockDraft(6);

        self::assertFalse($this->blockService->isPublished($block));
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
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            new BlockType('title', true, 'Title', 'title')
        );

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(1),
            'top_right',
            1
        );

        self::assertInstanceOf(APIBlockDraft::class, $block);

        $secondBlock = $this->blockService->loadBlockDraft(2);
        self::assertEquals(2, $secondBlock->getPosition());

        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        self::assertCount(1, $collectionReferences);

        self::assertEquals('default', $collectionReferences[0]->getIdentifier());
        self::assertEquals(0, $collectionReferences[0]->getOffset());
        self::assertNull($collectionReferences[0]->getLimit());

        $collection = $this->collectionService->loadCollectionDraft(5);
        self::assertEquals(Collection::TYPE_MANUAL, $collection->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockWithNonExistentLayoutType()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            new BlockType('title', true, 'Title', 'title')
        );

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(2),
            'top'
        );

        self::assertInstanceOf(APIBlockDraft::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockWithNoPosition()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            new BlockType('title', true, 'Title', 'title')
        );

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(1),
            'top_right'
        );

        self::assertInstanceOf(APIBlockDraft::class, $block);
        self::assertEquals(3, $block->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateBlockThrowsInvalidArgumentExceptionWhenPositionIsTooLarge()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            new BlockType('title', true, 'Title', 'title')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(1),
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
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            new BlockType('title', true, 'Title', 'title')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(1),
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
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            new BlockType('gallery', true, 'Gallery', 'gallery')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(1),
            'top_right'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlock()
    {
        $block = $this->blockService->loadBlockDraft(1);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertInstanceOf(APIBlockDraft::class, $block);
        self::assertEquals('small', $block->getViewType());
        self::assertEquals('Super cool block', $block->getName());
        self::assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'content' => 'Paragraph',
            ),
            $block->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlockWithBlankName()
    {
        $block = $this->blockService->loadBlockDraft(1);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertInstanceOf(APIBlockDraft::class, $block);
        self::assertEquals('small', $block->getViewType());
        self::assertEquals('My block', $block->getName());
        self::assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'content' => 'Paragraph',
            ),
            $block->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlockWithBlankViewType()
    {
        $block = $this->blockService->loadBlockDraft(1);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct();
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameter('test_param', 'test_value');
        $blockUpdateStruct->setParameter('some_other_test_param', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        self::assertInstanceOf(APIBlockDraft::class, $block);
        self::assertEquals('text', $block->getViewType());
        self::assertEquals('Super cool block', $block->getName());
        self::assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'content' => 'Paragraph',
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
            $this->blockService->loadBlockDraft(1)
        );

        self::assertInstanceOf(APIBlockDraft::class, $copiedBlock);
        self::assertEquals(7, $copiedBlock->getId());

        $copiedCollection = $this->collectionService->loadCollectionDraft(4);
        self::assertInstanceOf(Collection::class, $copiedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCopyBlockToDifferentZone()
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(1),
            'top_left'
        );

        self::assertInstanceOf(APIBlockDraft::class, $copiedBlock);
        self::assertEquals(7, $copiedBlock->getId());
        self::assertEquals('top_left', $copiedBlock->getZoneIdentifier());

        $copiedCollection = $this->collectionService->loadCollectionDraft(4);
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
            $this->blockService->loadBlockDraft(2),
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
            $this->blockService->loadBlockDraft(1),
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testMoveBlock()
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(1),
            1
        );

        self::assertInstanceOf(APIBlockDraft::class, $movedBlock);
        self::assertEquals(1, $movedBlock->getId());
        self::assertEquals(1, $movedBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(2);
        self::assertEquals(0, $secondBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testMoveBlockToDifferentZone()
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(2),
            0,
            'bottom'
        );

        self::assertInstanceOf(APIBlockDraft::class, $movedBlock);
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
            $this->blockService->loadBlockDraft(2),
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
            $this->blockService->loadBlockDraft(2),
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
            $this->blockService->loadBlockDraft(1),
            0,
            'bottom'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlock()
    {
        // First update a block

        $blockUpdateStruct = new BlockUpdateStruct(
            array(
                'viewType' => 'small',
                'itemViewType' => 'new',
                'name' => 'New name',
            )
        );

        $blockUpdateStruct->setParameter('content', 'new_value');

        $block = $this->blockService->loadBlockDraft(1);
        $updatedBlock = $this->blockService->updateBlock($block, $blockUpdateStruct);
        $movedBlock = $this->blockService->moveBlock($updatedBlock, 0, 'top_left');

        // Then verify that restored block has all published status' properties

        $restoredBlock = $this->blockService->restoreBlock($block);

        self::assertInstanceOf(APIBlockDraft::class, $restoredBlock);
        self::assertEquals('text', $restoredBlock->getViewType());
        self::assertEquals('standard', $restoredBlock->getItemViewType());
        self::assertEquals('My block', $restoredBlock->getName());
        self::assertEquals(array('content' => 'Paragraph'), $restoredBlock->getParameters());
        self::assertEquals($movedBlock->getPosition(), $restoredBlock->getPosition());
        self::assertEquals($movedBlock->getZoneIdentifier(), $restoredBlock->getZoneIdentifier());

        $collectionReferences = $this->blockService->loadCollectionReferences($restoredBlock);
        self::assertCount(2, $collectionReferences);

        self::assertEquals(2, $collectionReferences[0]->getCollection()->getId());
        self::assertEquals(3, $collectionReferences[1]->getCollection()->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testRestoreBlockThrowsBadStateException()
    {
        $block = $this->blockService->loadBlockDraft(6);

        $this->blockService->restoreBlock($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     */
    public function testDeleteBlock()
    {
        $block = $this->blockService->loadBlockDraft(1);
        $this->blockService->deleteBlock($block);

        try {
            $this->blockService->loadBlockDraft($block->getId());
            self::fail('Block still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondBlock = $this->blockService->loadBlockDraft(2);
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
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'css-class',
                    ),
                )
            ),
            $this->blockService->newBlockCreateStruct(
                new BlockType(
                    'title',
                    true,
                    'Title',
                    'title',
                    array(
                        'view_type' => 'small',
                        'item_view_type' => 'standard',
                        'name' => 'My block',
                        'parameters' => array(
                            'css_class' => 'css-class',
                        ),
                    )
                )
            )
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
