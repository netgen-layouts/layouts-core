<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistry;
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
use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Values\Page\BlockDraft as APIBlockDraft;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;

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
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

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
            '4_zones_a',
            true,
            '4 zones A',
            array(
                'left' => new LayoutTypeZone('left', 'Left', array()),
                'right' => new LayoutTypeZone('right', 'Right', array('title', 'list')),
                'bottom' => new LayoutTypeZone('bottom', 'Bottom', array('title')),
            )
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry();
        $this->layoutTypeRegistry->addLayoutType($layoutType);

        $blockDefinition1 = new BlockDefinition(
            'title',
            new BlockDefinitionHandler(),
            new Configuration('title')
        );

        $blockDefinition2 = new BlockDefinition(
            'gallery',
            new BlockDefinitionHandler(),
            new Configuration('gallery')
        );

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry();
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition1);
        $this->blockDefinitionRegistry->addBlockDefinition($blockDefinition2);

        $this->queryTypeRegistry = new QueryTypeRegistry();

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
            $this->collectionValidatorMock,
            $this->queryTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlock()
    {
        $block = $this->blockService->loadBlock(1);

        $this->assertInstanceOf(APIBlock::class, $block);
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

        $this->assertInstanceOf(APIBlockDraft::class, $block);
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

        $this->assertTrue($this->blockService->isPublished($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isPublished
     */
    public function testIsPublishedReturnsFalse()
    {
        $block = $this->blockService->loadBlockDraft(6);

        $this->assertFalse($this->blockService->isPublished($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadCollectionReference
     */
    public function testLoadCollectionReference()
    {
        $collection = $this->blockService->loadCollectionReference(
            $this->blockService->loadBlock(1),
            'default'
        );

        $this->assertInstanceOf(CollectionReference::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadCollectionReference
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadCollectionReferenceThrowsNotFoundException()
    {
        $collection = $this->blockService->loadCollectionReference(
            $this->blockService->loadBlock(1),
            'non_existing'
        );

        $this->assertInstanceOf(CollectionReference::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadCollectionReferences
     */
    public function testLoadCollectionReferences()
    {
        $collections = $this->blockService->loadCollectionReferences(
            $this->blockService->loadBlock(1)
        );

        $this->assertNotEmpty($collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionReference::class, $collection);
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
            'right',
            0
        );

        $this->assertInstanceOf(APIBlockDraft::class, $block);

        $secondBlock = $this->blockService->loadBlockDraft(1);
        $this->assertEquals(1, $secondBlock->getPosition());

        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        $this->assertCount(1, $collectionReferences);

        $this->assertEquals('default', $collectionReferences[0]->getIdentifier());
        $this->assertEquals(0, $collectionReferences[0]->getOffset());
        $this->assertNull($collectionReferences[0]->getLimit());

        $collection = $this->collectionService->loadCollectionDraft(6);
        $this->assertEquals(Collection::TYPE_MANUAL, $collection->getType());
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

        $this->assertInstanceOf(APIBlockDraft::class, $block);
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
            'right'
        );

        $this->assertInstanceOf(APIBlockDraft::class, $block);
        $this->assertEquals(2, $block->getPosition());
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
            'right',
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
            'right'
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

        $this->assertInstanceOf(APIBlockDraft::class, $block);
        $this->assertEquals('small', $block->getViewType());
        $this->assertEquals('Super cool block', $block->getName());
        $this->assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'number_of_columns' => 2,
            ),
            $block->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateCollectionReference
     */
    public function testUpdateCollectionReference()
    {
        $collectionReference = $this->blockService->loadCollectionReference(
            $this->blockService->loadBlockDraft(1),
            'default'
        );

        $newCollection = $this->collectionService->loadCollectionDraft(4);

        $updatedReference = $this->blockService->updateCollectionReference(
            $collectionReference,
            $newCollection
        );

        $this->assertInstanceOf(CollectionReference::class, $updatedReference);
        $this->assertEquals($newCollection->getId(), $updatedReference->getCollection()->getId());
        $this->assertEquals($newCollection->getStatus(), $updatedReference->getCollection()->getStatus());
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

        $this->assertInstanceOf(APIBlockDraft::class, $block);
        $this->assertEquals('small', $block->getViewType());
        $this->assertEquals('My block', $block->getName());
        $this->assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'number_of_columns' => 2,
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

        $this->assertInstanceOf(APIBlockDraft::class, $block);
        $this->assertEquals('list', $block->getViewType());
        $this->assertEquals('Super cool block', $block->getName());
        $this->assertEquals(
            array(
                'test_param' => 'test_value',
                'some_other_test_param' => 'some_other_test_value',
                'number_of_columns' => 2,
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

        $this->assertInstanceOf(APIBlockDraft::class, $copiedBlock);
        $this->assertEquals(7, $copiedBlock->getId());

        $copiedCollection = $this->collectionService->loadCollectionDraft(4);
        $this->assertInstanceOf(Collection::class, $copiedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCopyBlockToDifferentZone()
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(1),
            'left'
        );

        $this->assertInstanceOf(APIBlockDraft::class, $copiedBlock);
        $this->assertEquals(7, $copiedBlock->getId());
        $this->assertEquals('left', $copiedBlock->getZoneIdentifier());

        $copiedCollection = $this->collectionService->loadCollectionDraft(4);
        $this->assertInstanceOf(Collection::class, $copiedCollection);
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

        $this->assertInstanceOf(APIBlockDraft::class, $movedBlock);
        $this->assertEquals(1, $movedBlock->getId());
        $this->assertEquals(1, $movedBlock->getPosition());

        $secondBlock = $this->blockService->loadBlockDraft(2);
        $this->assertEquals(0, $secondBlock->getPosition());
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
            'left'
        );

        $this->assertInstanceOf(APIBlockDraft::class, $movedBlock);
        $this->assertEquals(2, $movedBlock->getId());
        $this->assertEquals('left', $movedBlock->getZoneIdentifier());
        $this->assertEquals(0, $movedBlock->getPosition());
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

        $blockUpdateStruct->setParameter('number_of_columns', 5);

        $block = $this->blockService->loadBlockDraft(1);
        $updatedBlock = $this->blockService->updateBlock($block, $blockUpdateStruct);
        $movedBlock = $this->blockService->moveBlock($updatedBlock, 0, 'left');

        // Then verify that restored block has all published status' properties

        $restoredBlock = $this->blockService->restoreBlock($block);

        $this->assertInstanceOf(APIBlockDraft::class, $restoredBlock);
        $this->assertEquals('list', $restoredBlock->getViewType());
        $this->assertEquals('standard', $restoredBlock->getItemViewType());
        $this->assertEquals('My block', $restoredBlock->getName());
        $this->assertEquals(array('number_of_columns' => 2), $restoredBlock->getParameters());
        $this->assertEquals($movedBlock->getPosition(), $restoredBlock->getPosition());
        $this->assertEquals($movedBlock->getZoneIdentifier(), $restoredBlock->getZoneIdentifier());

        $collectionReferences = $this->blockService->loadCollectionReferences($restoredBlock);
        $this->assertCount(2, $collectionReferences);

        $this->assertEquals(2, $collectionReferences[0]->getCollection()->getId());
        $this->assertEquals(3, $collectionReferences[1]->getCollection()->getId());
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
        $this->assertEquals(0, $secondBlock->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct()
    {
        $this->assertEquals(
            new BlockCreateStruct(
                array(
                    'definitionIdentifier' => 'title',
                    'viewType' => 'small',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameters' => array(
                        'css_class' => 'css-class',
                        'css_id' => null,
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
        $this->assertEquals(
            new BlockUpdateStruct(),
            $this->blockService->newBlockUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStructFromBlock()
    {
        $block = $this->blockService->loadBlockDraft(6);

        $this->assertEquals(
            new BlockUpdateStruct(
                array(
                    'viewType' => $block->getViewType(),
                    'itemViewType' => $block->getItemViewType(),
                    'name' => $block->getName(),
                    'parameters' => array(
                        'css_class' => 'CSS class',
                        'css_id' => null,
                    ),
                )
            ),
            $this->blockService->newBlockUpdateStruct($block)
        );
    }
}
