<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Parameters\ParameterValue;

abstract class BlockServiceTest extends ServiceTestCase
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
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->blockValidatorMock = $this->createMock(BlockValidator::class);
        $this->layoutValidatorMock = $this->createMock(LayoutValidator::class);
        $this->collectionValidatorMock = $this->createMock(CollectionValidator::class);

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
        $block = $this->blockService->loadBlock(1);

        $this->assertTrue($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
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

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedState()
    {
        $block = $this->blockService->loadBlock(1);

        $this->assertTrue($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedStateReturnsFalse()
    {
        $block = $this->blockService->loadBlockDraft(6);

        $this->assertFalse($this->blockService->hasPublishedState($block));
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
            new BlockType(
                'title',
                'Title',
                $this->blockDefinitionRegistry->getBlockDefinition('title')
            )
        );

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(1),
            'right',
            0
        );

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);

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
            new BlockType(
                'title',
                'Title',
                $this->blockDefinitionRegistry->getBlockDefinition('title')
            )
        );

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(2),
            'top'
        );

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockWithNoPosition()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            new BlockType(
                'title',
                'Title',
                $this->blockDefinitionRegistry->getBlockDefinition('title')
            )
        );

        $block = $this->blockService->createBlock(
            $blockCreateStruct,
            $this->layoutService->loadLayoutDraft(1),
            'right'
        );

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
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
            new BlockType(
                'title',
                'Title',
                $this->blockDefinitionRegistry->getBlockDefinition('title')
            )
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
            new BlockType(
                'title',
                'Title',
                $this->blockDefinitionRegistry->getBlockDefinition('title')
            )
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
            new BlockType(
                'gallery',
                'Gallery',
                $this->blockDefinitionRegistry->getBlockDefinition('gallery')
            )
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
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals('small', $block->getViewType());
        $this->assertEquals('Super cool block', $block->getName());
        $this->assertEquals(
            array(
                'css_class' => new ParameterValue(
                    array(
                        'name' => 'css_class',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'test_value',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_id'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'some_other_test_value',
                        'isEmpty' => false,
                    )
                ),
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
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals('small', $block->getViewType());
        $this->assertEquals('My block', $block->getName());
        $this->assertEquals(
            array(
                'css_class' => new ParameterValue(
                    array(
                        'name' => 'css_class',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'test_value',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_id'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'some_other_test_value',
                        'isEmpty' => false,
                    )
                ),
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
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $block = $this->blockService->updateBlock($block, $blockUpdateStruct);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals('list', $block->getViewType());
        $this->assertEquals('Super cool block', $block->getName());
        $this->assertEquals(
            array(
                'css_class' => new ParameterValue(
                    array(
                        'name' => 'css_class',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'test_value',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_id'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'some_other_test_value',
                        'isEmpty' => false,
                    )
                ),
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
        $this->assertEquals($newCollection->isPublished(), $updatedReference->getCollection()->isPublished());
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

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(7, $copiedBlock->getId());

        $copiedCollection = $this->collectionService->loadCollectionDraft(4);
        $this->assertFalse($copiedCollection->isPublished());
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

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(7, $copiedBlock->getId());
        $this->assertEquals('left', $copiedBlock->getZoneIdentifier());

        $copiedCollection = $this->collectionService->loadCollectionDraft(4);
        $this->assertFalse($copiedCollection->isPublished());
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

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
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

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
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
    public function testMoveBlockWithDisallowedIdentifierThrowsBadStateException()
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
        $block = $this->blockService->loadBlockDraft(1);
        $restoredBlock = $this->blockService->restoreBlock($block);

        $this->assertFalse($restoredBlock->isPublished());
        $this->assertInstanceOf(Block::class, $restoredBlock);
        $this->assertEquals('grid', $restoredBlock->getViewType());
        $this->assertEquals('standard_with_intro', $restoredBlock->getItemViewType());
        $this->assertEquals('My published block', $restoredBlock->getName());
        $this->assertEquals($block->getPosition(), $restoredBlock->getPosition());
        $this->assertEquals($block->getZoneIdentifier(), $restoredBlock->getZoneIdentifier());

        $this->assertEquals(
            array(
                'css_class' => new ParameterValue(
                    array(
                        'name' => 'css_class',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => null,
                        'isEmpty' => true,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_id'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => null,
                        'isEmpty' => true,
                    )
                ),
            ),
            $block->getParameters()
        );

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
                    'parameterValues' => array(
                        'css_class' => 'css-class',
                        'css_id' => null,
                    ),
                )
            ),
            $this->blockService->newBlockCreateStruct(
                new BlockType(
                    'title',
                    'Title',
                    $this->blockDefinitionRegistry->getBlockDefinition('title'),
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStructWithNonExistingViewType()
    {
        $this->assertEquals(
            new BlockCreateStruct(
                array(
                    'definitionIdentifier' => 'title',
                    'viewType' => 'small',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'css-class',
                        'css_id' => null,
                    ),
                )
            ),
            $this->blockService->newBlockCreateStruct(
                new BlockType(
                    'title',
                    'Title',
                    $this->blockDefinitionRegistry->getBlockDefinition('title'),
                    array(
                        'view_type' => 'non_existing',
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStructWithNonExistingItemViewType()
    {
        $this->assertEquals(
            new BlockCreateStruct(
                array(
                    'definitionIdentifier' => 'title',
                    'viewType' => 'small',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'css-class',
                        'css_id' => null,
                    ),
                )
            ),
            $this->blockService->newBlockCreateStruct(
                new BlockType(
                    'title',
                    'Title',
                    $this->blockDefinitionRegistry->getBlockDefinition('title'),
                    array(
                        'view_type' => 'small',
                        'item_view_type' => 'non_existing',
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
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStructWithNonExistingViewTypeAndItemViewType()
    {
        $this->assertEquals(
            new BlockCreateStruct(
                array(
                    'definitionIdentifier' => 'title',
                    'viewType' => 'small',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => array(
                        'css_class' => 'css-class',
                        'css_id' => null,
                    ),
                )
            ),
            $this->blockService->newBlockCreateStruct(
                new BlockType(
                    'title',
                    'Title',
                    $this->blockDefinitionRegistry->getBlockDefinition('title'),
                    array(
                        'view_type' => 'non_existing',
                        'item_view_type' => 'non_existing',
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
                    'parameterValues' => array(
                        'css_class' => 'CSS class',
                        'css_id' => null,
                    ),
                )
            ),
            $this->blockService->newBlockUpdateStruct($block)
        );
    }
}
