<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\Block\PlaceholderCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Parameters\ParameterValue;

abstract class BlockServiceTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->blockService = $this->createBlockService(
            $this->createMock(BlockValidator::class)
        );

        $this->layoutService = $this->createLayoutService(
            $this->createMock(LayoutValidator::class)
        );

        $this->collectionService = $this->createCollectionService(
            $this->createMock(CollectionValidator::class)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlock()
    {
        $block = $this->blockService->loadBlock(31);

        $this->assertTrue($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "999999"
     */
    public function testLoadBlockThrowsNotFoundException()
    {
        $this->blockService->loadBlock(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "1"
     */
    public function testLoadBlockThrowsNotFoundExceptionOnLoadingInternalBlock()
    {
        $this->blockService->loadBlock(1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     */
    public function testLoadBlockDraft()
    {
        $block = $this->blockService->loadBlockDraft(31);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "999999"
     */
    public function testLoadBlockDraftThrowsNotFoundException()
    {
        $this->blockService->loadBlockDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlockDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "1"
     */
    public function testLoadBlockDraftThrowsNotFoundExceptionOnLoadingInternalBlock()
    {
        $this->blockService->loadBlockDraft(1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadZoneBlocks
     */
    public function testLoadZoneBlocks()
    {
        $blocks = $this->blockService->loadZoneBlocks(
            $this->layoutService->loadZone(1, 'right')
        );

        $this->assertCount(2, $blocks);
        foreach ($blocks as $block) {
            $this->assertInstanceOf(Block::class, $block);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedState()
    {
        $block = $this->blockService->loadBlock(31);

        $this->assertTrue($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::hasPublishedState
     */
    public function testHasPublishedStateReturnsFalse()
    {
        $block = $this->blockService->loadBlockDraft(36);

        $this->assertFalse($this->blockService->hasPublishedState($block));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadCollectionReference
     */
    public function testLoadCollectionReference()
    {
        $collection = $this->blockService->loadCollectionReference(
            $this->blockService->loadBlock(31),
            'default'
        );

        $this->assertInstanceOf(CollectionReference::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadCollectionReference
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find collection reference with identifier "non_existing"
     */
    public function testLoadCollectionReferenceThrowsNotFoundException()
    {
        $collection = $this->blockService->loadCollectionReference(
            $this->blockService->loadBlock(31),
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
            $this->blockService->loadBlock(31)
        );

        $this->assertNotEmpty($collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionReference::class, $collection);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $targetBlock = $this->blockService->loadBlockDraft(33);

        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'main', 0);

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $mainPlaceholder = $targetBlock->getPlaceholder('main');

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $mainPlaceholder->getBlocks()[0]->getId());

        $this->assertEquals(37, $mainPlaceholder->getBlocks()[1]->getId());

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
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Blocks can only be created in blocks in draft status.
     */
    public function testCreateBlockThrowsBadStateExceptionWithNonDraftTargetBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlock(33),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Target block is not a container.
     */
    public function testCreateBlockThrowsBadStateExceptionWithNonContainerTargetBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(31),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.
     */
    public function testCreateBlockThrowsBadStateExceptionWithNoPlaceholder()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(33),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     */
    public function testCreateBlockWithNoPosition()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $targetBlock = $this->blockService->loadBlockDraft(33);

        $block = $this->blockService->createBlock($blockCreateStruct, $targetBlock, 'main');

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $mainPlaceholder = $targetBlock->getPlaceholder('main');

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $mainPlaceholder->getBlocks()[1]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCreateBlockThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(33),
            'main',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "blockCreateStruct" has an invalid state. Containers cannot be placed inside containers.
     */
    public function testCreateBlockThrowsBadStateExceptionWithContainerInsideContainer()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('column')
        );

        $this->blockService->createBlock(
            $blockCreateStruct,
            $this->blockService->loadBlockDraft(33),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockInZone()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('list')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $blocks[0]->getId());

        $this->assertEquals(31, $blocks[1]->getId());

        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        $this->assertCount(1, $collectionReferences);

        $this->assertEquals('default', $collectionReferences[0]->getIdentifier());
        $this->assertEquals(0, $collectionReferences[0]->getOffset());
        $this->assertNull($collectionReferences[0]->getLimit());

        $collection = $this->collectionService->loadCollectionDraft(6);
        $this->assertEquals(Collection::TYPE_MANUAL, $collection->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockInZoneWithContainerBlock()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('column')
        );

        $blockCreateStruct->setPlaceholderStruct('main', new PlaceholderCreateStruct());

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'left'),
            0
        );

        $this->assertCount(2, $block->getPlaceholders());

        $this->assertTrue($block->hasPlaceholder('main'));
        $this->assertTrue($block->hasPlaceholder('other'));

        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('main'));
        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('other'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockInZoneWithoutCollection()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $blocks[0]->getId());

        $this->assertEquals(31, $blocks[1]->getId());

        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        $this->assertCount(0, $collectionReferences);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockInZoneWhichDoesNotExistInLayoutType()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(7, 'center'),
            0
        );

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Blocks can only be created in zones in draft status.
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWithNonDraftZone()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZone(1, 'right'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockInZoneWithNonExistentLayoutType()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(2, 'top')
        );

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalCreateBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCreateBlockInZoneWithNoPosition()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $block = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right')
        );

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        $this->assertFalse($block->isPublished());
        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals($block->getId(), $blocks[2]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('title')
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right'),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::createBlockInZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Block is not allowed in specified zone.
     */
    public function testCreateBlockInZoneThrowsBadStateExceptionWithWithDisallowedIdentifier()
    {
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $this->blockDefinitionRegistry->getBlockDefinition('gallery')
        );

        $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $this->layoutService->loadZoneDraft(1, 'right')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateBlock
     */
    public function testUpdateBlock()
    {
        $block = $this->blockService->loadBlockDraft(31);

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
                        'parameter' => $block->getDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'test_value',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getDefinition()->getParameters()['css_id'],
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
        $block = $this->blockService->loadBlockDraft(31);

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
                        'parameter' => $block->getDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'test_value',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getDefinition()->getParameters()['css_id'],
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
        $block = $this->blockService->loadBlockDraft(31);

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
                        'parameter' => $block->getDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'test_value',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getDefinition()->getParameters()['css_id'],
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
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be updated.
     */
    public function testUpdateBlockThrowsBadStateExceptionWithNonDraftBlock()
    {
        $block = $this->blockService->loadBlock(31);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct();
        $blockUpdateStruct->viewType = 'small';
        $blockUpdateStruct->name = 'Super cool block';
        $blockUpdateStruct->setParameterValue('css_class', 'test_value');
        $blockUpdateStruct->setParameterValue('css_id', 'some_other_test_value');

        $this->blockService->updateBlock($block, $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::updateCollectionReference
     */
    public function testUpdateCollectionReference()
    {
        $collectionReference = $this->blockService->loadCollectionReference(
            $this->blockService->loadBlockDraft(31),
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
     */
    public function testCopyBlock()
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlockDraft(33),
            'main'
        );

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());

        $copiedCollection = $this->collectionService->loadCollectionDraft(4);
        $this->assertFalse($copiedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $copiedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be copied.
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonDraftBlock()
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlock(31),
            $this->blockService->loadBlockDraft(33),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. You can only copy blocks to draft blocks.
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonDraftTargetBlock()
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlock(33),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Target block is not a container.
     */
    public function testCopyBlockThrowsBadStateExceptionWithNonContainerTargetBlock()
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlockDraft(32),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.
     */
    public function testCopyBlockThrowsBadStateExceptionWithNoPlaceholder()
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlockDraft(33),
            'non_existing'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Containers cannot be placed inside containers.
     */
    public function testCopyBlockThrowsBadStateExceptionWithContainerInsideContainer()
    {
        $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft(33),
            $this->blockService->loadBlockDraft(38),
            'main'
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testCopyBlockToZone()
    {
        $copiedBlock = $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'left')
        );

        $this->assertFalse($copiedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $copiedBlock);
        $this->assertEquals(39, $copiedBlock->getId());

        $copiedCollection = $this->collectionService->loadCollectionDraft(4);
        $this->assertFalse($copiedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $copiedCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be copied.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithNonDraftBlock()
    {
        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlock(31),
            $this->layoutService->loadZoneDraft(1, 'left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. You can only copy blocks to draft zones.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithNonDraftZone()
    {
        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZone(1, 'left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::copyBlockToZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Block is not allowed in specified zone.
     */
    public function testCopyBlockToZoneThrowsBadStateExceptionWithDisallowedIdentifier()
    {
        $this->blockService->copyBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'bottom')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     */
    public function testMoveBlock()
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(32),
            $this->blockService->loadBlockDraft(33),
            'main',
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(32, $movedBlock->getId());

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $mainPlaceholder = $targetBlock->getPlaceholder('main');

        $this->assertEquals($movedBlock->getId(), $mainPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     */
    public function testMoveBlockToDifferentPlaceholder()
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(37),
            $this->blockService->loadBlockDraft(33),
            'other',
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(37, $movedBlock->getId());

        $targetBlock = $this->blockService->loadBlockDraft(33);
        $mainPlaceholder = $targetBlock->getPlaceholder('main');
        $otherPlaceholder = $targetBlock->getPlaceholder('other');

        $this->assertEmpty($mainPlaceholder->getBlocks());
        $this->assertEquals($movedBlock->getId(), $otherPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     */
    public function testMoveBlockToDifferentBlock()
    {
        $movedBlock = $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(37),
            $this->blockService->loadBlockDraft(38),
            'main',
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(37, $movedBlock->getId());

        $originalBlock = $this->blockService->loadBlockDraft(33);
        $targetBlock = $this->blockService->loadBlockDraft(38);
        $originalPlaceholder = $originalBlock->getPlaceholder('main');
        $targetPlaceholder = $targetBlock->getPlaceholder('main');

        $this->assertEmpty($originalPlaceholder->getBlocks());
        $this->assertEquals($movedBlock->getId(), $targetPlaceholder->getBlocks()[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be moved.
     */
    public function testMoveBlockThrowsBadStateExceptionWithNonDraftBlock()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlock(31),
            $this->blockService->loadBlockDraft(33),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. You can only move blocks to draft blocks.
     */
    public function testMoveBlockThrowsBadStateExceptionWithNonDraftTargetBlock()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlock(33),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "targetBlock" has an invalid state. Target block is not a container.
     */
    public function testMoveBlockThrowsBadStateExceptionWhenTargetBlockIsNotContainer()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(32),
            $this->blockService->loadBlockDraft(31),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.
     */
    public function testMoveBlockThrowsBadStateExceptionWithNoPlaceholder()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(31),
            $this->blockService->loadBlockDraft(33),
            'non_existing',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Containers cannot be placed inside containers.
     */
    public function testMoveBlockThrowsBadStateExceptionWithContainerInsideContainer()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(33),
            $this->blockService->loadBlockDraft(38),
            'main',
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveBlockThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft(32),
            $this->blockService->loadBlockDraft(33),
            'main',
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testMoveBlockToZone()
    {
        $movedBlock = $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(1, 'left'),
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(32, $movedBlock->getId());

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        $this->assertEquals($movedBlock->getId(), $blocks[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::internalMoveBlock
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     */
    public function testMoveBlockToDifferentZone()
    {
        $movedBlock = $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(1, 'right'),
            0
        );

        $this->assertFalse($movedBlock->isPublished());
        $this->assertInstanceOf(Block::class, $movedBlock);
        $this->assertEquals(32, $movedBlock->getId());

        $zone = $this->layoutService->loadZoneDraft(1, 'right');
        $blocks = $this->blockService->loadZoneBlocks($zone);

        $this->assertEquals($movedBlock->getId(), $blocks[0]->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be moved.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithNonDraftBlock()
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlock(31),
            $this->layoutService->loadZoneDraft(1, 'left'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. You can only move blocks to draft zones.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithNonDraftZone()
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZone(1, 'left'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "position" has an invalid state. Position is out of range.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'left'),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. You can only move block to zone in the same layout.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWhenZoneIsInDifferentLayout()
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(32),
            $this->layoutService->loadZoneDraft(2, 'bottom'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::moveBlockToZone
     * @covers \Netgen\BlockManager\Core\Service\BlockService::isBlockAllowedWithinZone
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "zone" has an invalid state. Block is not allowed in specified zone.
     */
    public function testMoveBlockToZoneThrowsBadStateExceptionWithDisallowedIdentifier()
    {
        $this->blockService->moveBlockToZone(
            $this->blockService->loadBlockDraft(31),
            $this->layoutService->loadZoneDraft(1, 'bottom'),
            0
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     */
    public function testRestoreBlock()
    {
        $block = $this->blockService->loadBlockDraft(31);
        $restoredBlock = $this->blockService->restoreBlock($block);

        $this->assertFalse($restoredBlock->isPublished());
        $this->assertInstanceOf(Block::class, $restoredBlock);
        $this->assertEquals('grid', $restoredBlock->getViewType());
        $this->assertEquals('standard_with_intro', $restoredBlock->getItemViewType());
        $this->assertEquals('My published block', $restoredBlock->getName());

        $this->assertEquals(
            array(
                'css_class' => new ParameterValue(
                    array(
                        'name' => 'css_class',
                        'parameter' => $block->getDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'some-class',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getDefinition()->getParameters()['css_id'],
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
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be restored.
     */
    public function testRestoreBlockThrowsBadStateExceptionWithNonDraftBlock()
    {
        $block = $this->blockService->loadBlock(31);

        $this->blockService->restoreBlock($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::restoreBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Block does not have a published status.
     */
    public function testRestoreBlockThrowsBadStateExceptionWithNoPublishedStatus()
    {
        $block = $this->blockService->loadBlockDraft(36);

        $this->blockService->restoreBlock($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "31"
     */
    public function testDeleteBlock()
    {
        $block = $this->blockService->loadBlockDraft(31);
        $this->blockService->deleteBlock($block);

        $this->blockService->loadBlockDraft($block->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::deleteBlock
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "block" has an invalid state. Only draft blocks can be deleted.
     */
    public function testDeleteThrowsBadStateExceptionBlockWithNonDraftBlock()
    {
        $block = $this->blockService->loadBlock(31);
        $this->blockService->deleteBlock($block);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct()
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition('title');

        $this->assertEquals(
            new BlockCreateStruct(
                array(
                    'definition' => $blockDefinition,
                    'viewType' => 'small',
                    'itemViewType' => 'standard',
                    'parameterValues' => array(
                        'css_class' => 'some-class',
                        'css_id' => null,
                    ),
                )
            ),
            $this->blockService->newBlockCreateStruct(
                $blockDefinition
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
        $block = $this->blockService->loadBlockDraft(36);

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
